@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen px-4 py-12 gap-6 text-center">
    <x-logo type="icon" class="w-14 h-14" />

    <div class="space-y-2">
        <h1 class="text-2xl font-bold font-title">Coloc introuvable</h1>
        <p class="text-sm text-muted-foreground">Cette coloc n'existe pas... ou alors elle a demenage sans prevenir.</p>
    </div>

    <a href="{{ route('landing') }}" class="bg-coral hover:bg-coral-dark text-cream font-title font-bold rounded-2xl py-3 px-8 transition-colors">
        Creer une coloc
    </a>
</div>
@endsection
