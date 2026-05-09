@props(['coloc', 'task'])

<div class="bg-card border border-border rounded-2xl p-4 flex flex-col items-center gap-2">
    <img src="{{ asset('images/tasks/' . $task->icon_slug . '.svg') }}" alt="{{ $task->name }}" class="w-8 h-8">
    <span class="text-xs font-title font-bold text-center">{{ $task->name }}</span>
    <img src="{{ route('coloc.qr.task', [$coloc, $task]) }}" alt="QR {{ $task->name }}" class="w-28 h-28">
</div>
