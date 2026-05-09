@props(['task', 'roommate', 'completion' => null])

@php
$status = $completion?->status;
$borderClass = match($status) {
    'done' => 'border-green-300 bg-green-50/50 opacity-75',
    'not_done' => 'border-coral/50 bg-coral/5',
    'done_by_other' => 'border-amber-300 bg-amber-50/50 opacity-75',
    default => 'border-border bg-card hover:border-coral/50 hover:shadow-sm',
};
$statusIcon = match($status) {
    'done' => '✓',
    'not_done' => '✗',
    'done_by_other' => '↺',
    default => null,
};
@endphp

<button
    type="button"
    class="w-full flex items-center gap-4 rounded-2xl border-2 p-4 transition-all {{ $borderClass }}"
    data-task-id="{{ $task->id }}"
    data-task-name="{{ $task->name }}"
    data-task-icon="{{ $task->icon_slug }}"
    data-roommate-id="{{ $roommate->id }}"
    data-roommate-name="{{ $roommate->first_name }}"
    data-roommate-avatar="{{ $roommate->avatar_url ? asset('storage/' . $roommate->avatar_url) : asset('images/avatars/' . $roommate->avatar_slug . '.png') }}"
    @click="openCompletion($event.currentTarget.dataset)"
>
    <img src="{{ asset('images/tasks/' . $task->icon_slug . '.svg') }}" alt="{{ $task->name }}" class="w-10 h-10 lg:w-12 lg:h-12 shrink-0">

    <div class="flex-1 text-left min-w-0">
        <p class="font-title font-bold text-ink text-sm lg:text-base truncate">{{ $task->name }}</p>
        @if($status)
            <p class="text-xs text-muted-foreground mt-0.5">
                @if($status === 'done') Fait !
                @elseif($status === 'not_done') Pas fait...
                @elseif($status === 'done_by_other') Fait par {{ $completion->actualRoommate?->first_name ?? 'quelqu\'un' }}
                @endif
            </p>
        @endif
    </div>

    <div class="flex items-center gap-2 shrink-0">
        <div class="text-right hidden sm:block">
            <span class="text-sm text-muted-foreground">{{ $roommate->first_name }}</span>
        </div>
        <img src="{{ $roommate->avatar_url ? asset('storage/' . $roommate->avatar_url) : asset('images/avatars/' . $roommate->avatar_slug . '.png') }}" alt="{{ $roommate->first_name }}" class="w-10 h-10 lg:w-12 lg:h-12 rounded-full object-cover">
    </div>

    @if($statusIcon)
        <span class="text-lg shrink-0
            @if($status === 'done') text-green-400
            @elseif($status === 'not_done') text-coral
            @else text-amber-400
            @endif
        ">{{ $statusIcon }}</span>
    @endif
</button>
