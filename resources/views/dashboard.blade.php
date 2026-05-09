@extends('layouts.app')

@section('content')
<div x-data="completionManager()" class="min-h-screen flex flex-col">
    <x-header :coloc="$coloc" />

    <main class="flex-1 px-4 py-6 lg:py-10 max-w-md lg:max-w-3xl xl:max-w-4xl mx-auto w-full space-y-6 lg:space-y-8">
        <div class="text-center space-y-1">
            <h1 class="text-xl lg:text-2xl font-bold font-title">{{ $coloc->name }}</h1>
            <p class="text-xs lg:text-sm text-muted-foreground">{{ $coloc->roommates->count() }} coloc(s)</p>
        </div>

        {{-- Urgent todos --}}
        @if($urgentTodos->count() > 0 || true)
        <section x-data="{ showForm: false }" class="bg-card border-2 border-coral/30 rounded-2xl p-4 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-title font-bold text-coral flex items-center gap-2">
                    <span class="text-lg">!</span> À faire maintenant
                </h2>
                <button type="button" @click="showForm = !showForm" class="text-xs text-coral font-title font-bold">+ Ajouter</button>
            </div>

            <div x-show="showForm" x-cloak>
                <form action="{{ route('coloc.todo.store', $coloc) }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="title" placeholder="ex: Sortir les poubelles, la porte grince..." maxlength="100" required class="flex-1 text-sm bg-cream border border-border rounded-xl px-3 py-2 font-body text-ink placeholder:text-muted-foreground focus:outline-none focus:border-coral">
                    <button type="submit" class="bg-coral text-cream rounded-xl px-4 py-2 text-sm font-title font-bold shrink-0">OK</button>
                </form>
            </div>

            @if($urgentTodos->count() > 0)
                <div class="space-y-2">
                    @foreach($urgentTodos as $todo)
                        <div class="flex items-center gap-3 bg-cream rounded-xl px-3 py-2">
                            <form action="{{ route('coloc.todo.complete', [$coloc, $todo]) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-6 h-6 rounded-full border-2 border-coral/50 hover:bg-coral/10 transition-colors flex items-center justify-center shrink-0">
                                </button>
                            </form>
                            <span class="text-sm text-ink flex-1">{{ $todo->title }}</span>
                            <form action="{{ route('coloc.todo.destroy', [$coloc, $todo]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-muted-foreground hover:text-coral">&times;</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-muted-foreground text-center py-1">Rien d'urgent pour le moment</p>
            @endif
        </section>
        @endif

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
            <div class="flex justify-center gap-6 flex-wrap">
                <a href="{{ route('coloc.stats', $coloc) }}" class="inline-flex items-center gap-2 text-sm text-coral hover:underline font-title font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                    Stats
                </a>
                <a href="{{ route('coloc.history', $coloc) }}" class="inline-flex items-center gap-2 text-sm text-coral hover:underline font-title font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>
                    Historique
                </a>
                <a href="{{ route('coloc.qr', $coloc) }}" class="inline-flex items-center gap-2 text-sm text-coral hover:underline font-title font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/></svg>
                    QR Codes
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
