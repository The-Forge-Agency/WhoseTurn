@extends('layouts.app')

@section('content')
<div x-data="completionManager()" class="min-h-screen flex flex-col">
    <x-header :coloc="$coloc" />

    <main class="flex-1 px-4 py-6 lg:py-10 max-w-md lg:max-w-3xl xl:max-w-4xl mx-auto w-full space-y-6 lg:space-y-8">
        <div class="text-center space-y-1">
            <h1 class="text-xl lg:text-2xl font-bold font-title">{{ $coloc->name }}</h1>
            <p class="text-xs lg:text-sm text-muted-foreground">{{ $coloc->roommates->count() }} coloc(s)</p>
        </div>

        <x-week-header :weekRange="$weekRange" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 lg:gap-4">
            @foreach($assignments as $assignment)
                <x-task-card
                    :task="$assignment['task']"
                    :roommate="$assignment['roommate']"
                    :completion="$completions->get($assignment['task']->id)"
                />
            @endforeach
        </div>

        <div class="pt-6 space-y-3 max-w-md mx-auto w-full">
            <x-share-link :coloc="$coloc" />
            <div class="text-center">
                <a href="{{ route('coloc.qr', $coloc) }}" class="inline-flex items-center gap-2 text-sm text-coral hover:underline font-title font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/></svg>
                    QR Codes a imprimer
                </a>
            </div>
        </div>
    </main>

    @include('components.completion-dialog', ['coloc' => $coloc])
</div>

@push('scripts')
<script>
function completionManager() {
    return {
        open: false,
        taskId: null,
        taskName: '',
        taskIcon: '',
        assignedRoommateId: null,
        assignedRoommateName: '',
        assignedRoommateAvatar: '',
        step: 'choice',
        loading: false,

        openCompletion(data) {
            this.taskId = data.taskId;
            this.taskName = data.taskName;
            this.taskIcon = data.taskIcon;
            this.assignedRoommateId = data.roommateId;
            this.assignedRoommateName = data.roommateName;
            this.assignedRoommateAvatar = data.roommateAvatar;
            this.step = 'choice';
            this.open = true;
        },

        async submitCompletion(status, actualRoommateId = null) {
            this.loading = true;
            try {
                const res = await fetch('{{ route("coloc.complete", $coloc) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        task_id: this.taskId,
                        assigned_roommate_id: this.assignedRoommateId,
                        status: status,
                        actual_roommate_id: actualRoommateId,
                    }),
                });
                if (res.ok) {
                    this.open = false;
                    window.location.reload();
                }
            } finally {
                this.loading = false;
            }
        },

        close() {
            this.open = false;
        }
    }
}
</script>
@endpush
@endsection
