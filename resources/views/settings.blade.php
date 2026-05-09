@extends('layouts.app')

@section('content')
<div class="min-h-screen px-4 py-6 lg:py-10">
    <div class="max-w-md lg:max-w-3xl mx-auto space-y-6 lg:space-y-8">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-xl lg:text-2xl font-bold font-title">Reglages</h1>
            <a href="{{ route('coloc.dashboard', $coloc) }}" class="text-coral font-title font-bold text-sm hover:underline">← Retour</a>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <p class="text-green-600 text-sm font-body bg-green-50 rounded-2xl px-4 py-2">{{ session('success') }}</p>
        @endif
        @if(session('warning'))
            <p class="text-amber-600 text-sm font-body bg-amber-50 rounded-2xl px-4 py-2">{{ session('warning') }}</p>
        @endif

        <div class="lg:grid lg:grid-cols-2 lg:gap-8 space-y-8 lg:space-y-0">
            {{-- Colocs section --}}
            <section class="space-y-4">
                <h2 class="font-title font-bold text-lg">Colocataires</h2>

                @if($roommates->count() > 0)
                    <div class="space-y-2">
                        @foreach($roommates as $roommate)
                            <div class="flex items-center gap-3 bg-card border border-border rounded-2xl px-4 py-3">
                                <img src="{{ asset('images/avatars/' . $roommate->avatar_slug . '.png') }}" alt="{{ $roommate->first_name }}" class="w-10 h-10 rounded-full object-cover">
                                <span class="text-sm text-ink flex-1 font-title font-bold">{{ $roommate->first_name }}</span>
                                <form action="{{ route('coloc.settings.roommate.destroy', [$coloc, $roommate]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-body text-muted-foreground hover:text-coral transition-colors">Retirer</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-muted-foreground py-4">Aucun coloc pour l'instant.</p>
                @endif

                <div class="border-t border-border pt-4">
                    <p class="text-sm font-title font-bold mb-3">Ajouter un coloc</p>
                    <form action="{{ route('coloc.settings.roommate.store', $coloc) }}" method="POST" class="space-y-4">
                        @csrf
                        <x-input name="first_name" placeholder="Prenom du nouveau coloc" maxlength="30" value="{{ old('first_name') }}" required />
                        @error('first_name')
                            <p class="text-coral text-xs font-body">{{ $message }}</p>
                        @enderror

                        <x-avatar-picker :takenAvatars="$takenAvatars" :selected="old('avatar_slug', '')" />
                        @error('avatar_slug')
                            <p class="text-coral text-xs font-body">{{ $message }}</p>
                        @enderror

                        <x-button variant="dashed">+ Ajouter</x-button>
                    </form>
                </div>
            </section>

            {{-- Tasks section --}}
            <section class="space-y-4">
                <h2 class="font-title font-bold text-lg">Taches</h2>

                <form action="{{ route('coloc.settings.tasks.store', $coloc) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($tasks as $task)
                            <x-task-toggle :task="$task" :checked="$task->enabled" />
                        @endforeach
                    </div>

                    @error('tasks')
                        <p class="text-coral text-xs font-body text-center">{{ $message }}</p>
                    @enderror

                    <x-button>Sauvegarder</x-button>
                </form>
            </section>
        </div>

        {{-- Share link --}}
        <div class="max-w-md mx-auto pt-4">
            <x-share-link :coloc="$coloc" />
        </div>
    </div>
</div>
@endsection
