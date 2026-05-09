@extends('layouts.app')

@section('content')
<div class="min-h-screen px-4 py-6 lg:py-10">
    <div class="max-w-md lg:max-w-3xl mx-auto space-y-6 lg:space-y-8">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-xl lg:text-2xl font-bold font-title">Réglages</h1>
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
                                <img src="{{ $roommate->avatar_url ? asset('storage/' . $roommate->avatar_url) : asset('images/avatars/' . $roommate->avatar_slug . '.png') }}" alt="{{ $roommate->first_name }}" class="w-10 h-10 rounded-full object-cover">
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
                    <form action="{{ route('coloc.settings.roommate.store', $coloc) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <x-input name="first_name" placeholder="Prénom du nouveau coloc" maxlength="30" value="{{ old('first_name') }}" required />
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
                <h2 class="font-title font-bold text-lg">Tâches</h2>

                {{-- Toggle existing tasks --}}
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

                {{-- Delete tasks list --}}
                @if($tasks->count() > 0)
                    <div class="border-t border-border pt-4">
                        <p class="text-xs text-muted-foreground mb-2">Supprimer une tâche :</p>
                        <div class="space-y-1">
                            @foreach($tasks as $task)
                                <div class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-muted transition-colors">
                                    <img src="{{ asset('images/tasks/' . $task->icon_slug . '.svg') }}" alt="{{ $task->name }}" class="w-5 h-5">
                                    <span class="text-xs text-ink flex-1">{{ $task->name }}</span>
                                    <form action="{{ route('coloc.settings.task.destroy', [$coloc, $task]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-muted-foreground hover:text-coral transition-colors">&times;</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Add custom task --}}
                <div class="border-t border-border pt-4">
                    <p class="text-sm font-title font-bold mb-3">Ajouter une tâche</p>
                    <form action="{{ route('coloc.settings.task.create', $coloc) }}" method="POST" class="space-y-4">
                        @csrf
                        <x-input name="name" placeholder="ex: Sortir le chien" maxlength="40" value="{{ old('name') }}" required />
                        @error('name')
                            <p class="text-coral text-xs font-body">{{ $message }}</p>
                        @enderror

                        <div x-data="{ selectedIcon: '{{ old('icon_slug', 'custom') }}' }">
                            <p class="text-xs text-muted-foreground mb-2">Choisis une icône :</p>
                            <div class="grid grid-cols-5 sm:grid-cols-6 gap-2">
                                @foreach(['vaisselle', 'poubelle', 'aspirateur', 'courses', 'cuisine', 'lessive', 'salle-de-bain', 'toilettes', 'salon', 'serpilliere'] as $icon)
                                    <button
                                        type="button"
                                        @click="selectedIcon = '{{ $icon }}'"
                                        :class="selectedIcon === '{{ $icon }}' ? 'ring-2 ring-coral bg-coral/10 scale-105' : 'hover:ring-1 hover:ring-coral/50'"
                                        class="flex items-center justify-center p-2 rounded-xl border border-border transition-all"
                                    >
                                        <img src="{{ asset('images/tasks/' . $icon . '.svg') }}" alt="{{ $icon }}" class="w-6 h-6">
                                    </button>
                                @endforeach
                            </div>
                            <input type="hidden" name="icon_slug" :value="selectedIcon">
                        </div>
                        @error('icon_slug')
                            <p class="text-coral text-xs font-body">{{ $message }}</p>
                        @enderror

                        <x-button variant="dashed">+ Ajouter cette tâche</x-button>
                    </form>
                </div>
            </section>
        </div>

        {{-- Share link --}}
        <div class="max-w-md mx-auto pt-4">
            <x-share-link :coloc="$coloc" />
        </div>
    </div>
</div>
@endsection
