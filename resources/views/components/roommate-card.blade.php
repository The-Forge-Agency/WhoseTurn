@props(['roommate', 'deleteRoute'])

<div class="flex items-center gap-3 bg-card border border-border rounded-2xl px-3 py-2">
    <img
        src="{{ $roommate->avatar_url ? asset('storage/' . $roommate->avatar_url) : asset('images/avatars/' . $roommate->avatar_slug . '.png') }}"
        alt="{{ $roommate->first_name }}"
        class="w-10 h-10 rounded-full object-cover"
    >
    <span class="text-sm text-ink flex-1">{{ $roommate->first_name }}</span>
    <form action="{{ $deleteRoute }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-muted-foreground hover:text-coral text-sm transition-colors">&times;</button>
    </form>
</div>
