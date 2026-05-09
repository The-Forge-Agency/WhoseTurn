@extends('layouts.app')

@section('content')
<div class="min-h-screen px-4 py-6 lg:py-10" x-data="{ step: {{ $currentStep }} }">
    <div class="max-w-md lg:max-w-2xl mx-auto space-y-6 lg:space-y-8">
        <div class="text-center space-y-2">
            <h1 class="text-2xl lg:text-3xl font-bold font-title">{{ $coloc->name }}</h1>
            <p class="text-sm lg:text-base text-muted-foreground" x-show="step === 1">Qui habite ici ?</p>
            <p class="text-sm lg:text-base text-muted-foreground" x-show="step === 2" x-cloak>Quelles tâches pour la coloc ?</p>
        </div>

        <x-step-indicator :currentStep="$currentStep" />

        {{-- Step 1: Roommates --}}
        <div x-show="step === 1">
            <div class="lg:grid lg:grid-cols-2 lg:gap-8">
                {{-- Existing roommates --}}
                <div>
                    @if($roommates->count() > 0)
                        <div class="space-y-2 mb-4">
                            <p class="text-sm font-title font-bold">Dans la coloc ({{ $roommates->count() }})</p>
                            @foreach($roommates as $roommate)
                                <x-roommate-card
                                    :roommate="$roommate"
                                    :deleteRoute="route('coloc.setup.roommate.destroy', [$coloc, $roommate])"
                                />
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted-foreground text-sm py-8 mb-4 lg:mb-0">
                            <p>Aucun coloc pour l'instant.</p>
                            <p class="mt-1">Ajoute au moins 2 personnes.</p>
                        </div>
                    @endif
                </div>

                {{-- Add form --}}
                <div>
                    <form action="{{ route('coloc.setup.roommate.store', $coloc) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="space-y-2">
                            <x-input name="first_name" placeholder="Prénom" maxlength="30" value="{{ old('first_name') }}" required />
                            @error('first_name')
                                <p class="text-coral text-xs font-body">{{ $message }}</p>
                            @enderror
                        </div>

                        <x-avatar-picker :takenAvatars="$takenAvatars" :selected="old('avatar_slug', '')" />
                        @error('avatar_slug')
                            <p class="text-coral text-xs font-body">{{ $message }}</p>
                        @enderror

                        <x-button variant="dashed">+ Ajouter ce coloc</x-button>
                    </form>
                </div>
            </div>

            <div class="mt-6 max-w-sm mx-auto">
                @if($roommates->count() >= 2)
                    <x-button type="button" @click="step = 2">Suivant</x-button>
                @else
                    <x-button type="button" disabled>Suivant</x-button>
                    <p class="text-xs text-muted-foreground text-center mt-2">Ajoute au moins 2 colocs pour continuer</p>
                @endif
            </div>
        </div>

        {{-- Step 2: Tasks --}}
        <div x-show="step === 2" x-cloak>
            <form action="{{ route('coloc.setup.tasks.store', $coloc) }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($tasks as $task)
                        <x-task-toggle :task="$task" :checked="$task->enabled" />
                    @endforeach
                </div>

                @error('tasks')
                    <p class="text-coral text-xs font-body text-center">{{ $message }}</p>
                @enderror

                <div class="flex gap-3 mt-6 max-w-sm mx-auto">
                    <x-button type="button" variant="secondary" @click="step = 1" class="flex-1">Retour</x-button>
                    <x-button class="flex-1">C'est parti !</x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
