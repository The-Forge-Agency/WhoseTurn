@props(['task', 'checked' => true])

<div
    x-data="{ enabled: {{ $checked ? 'true' : 'false' }} }"
    @click="enabled = !enabled"
    :class="enabled ? 'border-coral bg-coral/10' : 'border-border opacity-50'"
    class="flex flex-col items-center gap-2 border-2 rounded-2xl px-4 py-3 cursor-pointer transition-all select-none"
>
    <img src="{{ $task->icon_src }}" alt="{{ $task->name }}" class="w-8 h-8 rounded-lg object-cover">
    <span class="text-xs font-title font-bold text-ink">{{ $task->name }}</span>
    <input
        type="checkbox"
        name="tasks[]"
        value="{{ $task->id }}"
        x-bind:checked="enabled"
        class="hidden"
    >
</div>
