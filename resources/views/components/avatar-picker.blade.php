@props(['takenAvatars' => [], 'selected' => ''])

<div x-data="{ selected: '{{ $selected }}' }" class="space-y-2">
    <div class="grid grid-cols-4 gap-3">
        @foreach(\App\Models\Roommate::AVATARS as $avatar)
            @php $isTaken = in_array($avatar, $takenAvatars, true); @endphp
            <button
                type="button"
                @if(!$isTaken)
                    @click="selected = '{{ $avatar }}'"
                @endif
                :class="selected === '{{ $avatar }}' ? 'ring-3 ring-coral bg-coral/10 scale-105' : ''"
                class="relative rounded-2xl p-1 transition-all
                    {{ $isTaken ? 'opacity-30 cursor-not-allowed' : 'hover:ring-2 hover:ring-coral/50 hover:scale-105 cursor-pointer' }}"
                {{ $isTaken ? 'disabled' : '' }}
            >
                <img
                    src="{{ asset('images/avatars/' . $avatar . '.png') }}"
                    alt="{{ $avatar }}"
                    class="w-full aspect-square rounded-xl object-cover"
                >
            </button>
        @endforeach
    </div>
    <input type="hidden" name="avatar_slug" :value="selected">
</div>
