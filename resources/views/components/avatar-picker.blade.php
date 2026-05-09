@props(['takenAvatars' => [], 'selected' => ''])

<div x-data="{ selected: '{{ $selected }}', usePhoto: false, photoPreview: null }" class="space-y-3">
    {{-- Photo upload option --}}
    <div class="flex items-center gap-3">
        <label
            class="flex items-center gap-2 cursor-pointer text-sm font-title font-bold text-coral hover:underline"
            @click="usePhoto = !usePhoto"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
            <span x-text="usePhoto ? 'Utiliser un avatar' : 'Utiliser une photo'"></span>
        </label>
    </div>

    {{-- Photo upload --}}
    <div x-show="usePhoto" x-cloak class="space-y-2">
        <div class="flex items-center gap-4">
            <template x-if="photoPreview">
                <img :src="photoPreview" class="w-16 h-16 rounded-full object-cover border-2 border-coral">
            </template>
            <template x-if="!photoPreview">
                <div class="w-16 h-16 rounded-full bg-muted border-2 border-dashed border-border flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted-foreground"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                </div>
            </template>
            <label class="cursor-pointer bg-coral hover:bg-coral-dark text-cream rounded-xl px-4 py-2 text-xs font-title font-bold transition-colors">
                Choisir une photo
                <input
                    type="file"
                    name="avatar_photo"
                    accept="image/*"
                    capture="environment"
                    class="hidden"
                    @change="
                        const file = $event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (e) => photoPreview = e.target.result;
                            reader.readAsDataURL(file);
                            selected = '';
                        }
                    "
                >
            </label>
        </div>
        <p class="text-xs text-muted-foreground">JPG, PNG — max 2 Mo</p>
    </div>

    {{-- Preset avatars --}}
    <div x-show="!usePhoto">
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
    </div>
    <input type="hidden" name="avatar_slug" :value="selected">
</div>
