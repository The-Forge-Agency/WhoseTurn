{{-- Bottom-sheet dialog for task completion --}}
<template x-if="open">
    <div class="fixed inset-0 z-50" @keydown.escape.window="close()">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-ink/40" @click="close()"></div>

        {{-- Modal --}}
        <div class="absolute inset-x-0 bottom-0 sm:inset-0 sm:flex sm:items-center sm:justify-center">
            <div class="bg-cream rounded-t-3xl sm:rounded-3xl p-6 space-y-5 w-full sm:max-w-md" @click.stop>
                {{-- Header --}}
                <div class="flex items-center gap-4">
                    <img :src="taskIcon" :alt="taskName" class="w-12 h-12 rounded-xl object-cover">
                    <div class="flex-1">
                        <p class="text-lg font-bold font-title" x-text="taskName"></p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-sm text-muted-foreground">C'est le tour de</span>
                            <span class="text-sm font-bold font-title" x-text="assignedRoommateName"></span>
                            <img :src="assignedRoommateAvatar" :alt="assignedRoommateName" class="w-8 h-8 rounded-full object-cover">
                        </div>
                    </div>
                </div>

                {{-- Choice buttons --}}
                <div x-show="step === 'choice'" class="space-y-3">
                    <button
                        type="button"
                        @click="submitCompletion('done')"
                        :disabled="loading"
                        class="w-full flex items-center gap-4 border-2 border-green-400 bg-green-50 rounded-2xl px-4 py-4 hover:bg-green-100 transition-colors disabled:opacity-50"
                    >
                        <span class="text-2xl">✓</span>
                        <span class="font-title font-bold text-ink">Fait !</span>
                    </button>

                    <button
                        type="button"
                        @click="submitCompletion('not_done')"
                        :disabled="loading"
                        class="w-full flex items-center gap-4 border-2 border-coral bg-coral/10 rounded-2xl px-4 py-4 hover:bg-coral/20 transition-colors disabled:opacity-50"
                    >
                        <span class="text-2xl text-coral">✗</span>
                        <div>
                            <span class="font-title font-bold text-ink">Pas fait</span>
                            <span class="text-xs text-muted-foreground ml-1">(la honte)</span>
                        </div>
                    </button>

                    <button
                        type="button"
                        @click="step = 'picker'"
                        class="w-full flex items-center gap-4 border-2 border-border rounded-2xl px-4 py-4 hover:border-coral/50 hover:bg-card transition-colors"
                    >
                        <span class="text-2xl">↺</span>
                        <span class="font-title font-bold text-ink">Fait par quelqu'un d'autre</span>
                    </button>
                </div>

                {{-- Roommate picker --}}
                <div x-show="step === 'picker'" x-cloak class="space-y-3">
                    <p class="font-title font-bold text-sm">Qui a fait cette tache ?</p>
                    @foreach($coloc->roommates as $roommate)
                        <button
                            type="button"
                            x-show="'{{ $roommate->id }}' !== assignedRoommateId"
                            @click="submitCompletion('done_by_other', '{{ $roommate->id }}')"
                            :disabled="loading"
                            class="w-full flex items-center gap-3 rounded-2xl border-2 border-border bg-card px-4 py-3 hover:border-coral transition-colors disabled:opacity-50"
                        >
                            <img src="{{ $roommate->avatar_url ? asset('storage/' . $roommate->avatar_url) : asset('images/avatars/' . $roommate->avatar_slug . '.png') }}" alt="{{ $roommate->first_name }}" class="w-10 h-10 rounded-full object-cover">
                            <span class="text-sm text-ink">{{ $roommate->first_name }}</span>
                        </button>
                    @endforeach

                    <button type="button" @click="step = 'choice'" class="w-full text-sm text-muted-foreground py-2 text-center">Retour</button>
                </div>
            </div>
        </div>
    </div>
</template>
