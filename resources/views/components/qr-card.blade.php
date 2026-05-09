@props(['coloc', 'task'])

<div class="bg-card border border-border rounded-2xl p-4 flex flex-col items-center gap-2 print-qr-card">
    <img src="{{ $task->icon_src }}" alt="{{ $task->name }}" class="w-8 h-8 rounded-lg object-cover">
    <span class="text-xs font-title font-bold text-center">{{ $task->name }}</span>
    <img src="{{ route('coloc.qr.task', [$coloc, $task]) }}" alt="QR {{ $task->name }}" class="w-28 h-28">
    <p class="text-[10px] text-muted-foreground text-center break-all leading-tight print-url">{{ route('coloc.task', [$coloc, $task]) }}</p>
    <a
        href="{{ route('coloc.qr.task', [$coloc, $task]) }}"
        download="qr-{{ Str::slug($task->name) }}.svg"
        class="text-[10px] text-coral hover:underline font-title font-bold print:hidden"
    >
        Télécharger
    </a>
</div>
