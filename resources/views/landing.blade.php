@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen px-4 py-8">
    <div class="w-full max-w-sm sm:max-w-md flex flex-col items-center gap-8">
        <x-logo type="icon" class="w-20 h-20" />

        <div class="text-center space-y-3">
            <h1 class="text-3xl sm:text-4xl font-bold font-title">WhoseTurn</h1>
            <p class="text-sm sm:text-base text-muted-foreground leading-relaxed">En coloc, il y a toujours quelqu'un qui fait tout... et quelqu'un qui ne voit rien.</p>
            <p class="text-lg sm:text-xl text-coral font-bold font-title">Plus d'excuses. Plus d'embrouilles.</p>
        </div>

        <form action="{{ route('coloc.store') }}" method="POST" class="w-full space-y-4">
            @csrf
            <div class="space-y-2">
                <label for="name" class="text-sm font-title font-bold">Nom de ta coloc</label>
                <x-input name="name" id="name" placeholder="ex: L'appart du 42" maxlength="50" value="{{ old('name') }}" required />
                @error('name')
                    <p class="text-coral text-xs font-body mt-1">{{ $message }}</p>
                @enderror
            </div>

            <x-button>Créer ma coloc</x-button>
        </form>

        <p class="text-xs text-muted-foreground">Zéro inscription. Juste un lien à partager.</p>
    </div>
</div>
@endsection
