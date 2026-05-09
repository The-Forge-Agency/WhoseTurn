@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col">
    <x-header :coloc="$coloc" />

    <main class="flex-1 px-4 py-6 lg:py-10 max-w-sm lg:max-w-lg mx-auto w-full flex flex-col items-center justify-center gap-6">
        <div class="bg-card border border-border rounded-2xl p-8 lg:p-10 w-full flex flex-col items-center gap-6">
            <img src="{{ asset('images/tasks/' . $task->icon_slug . '.svg') }}" alt="{{ $task->name }}" class="w-16 h-16 lg:w-20 lg:h-20">
            <h1 class="text-2xl lg:text-3xl font-bold font-title">{{ $task->name }}</h1>

            @if($roommate)
                <p class="text-sm text-muted-foreground text-center">{{ $weekRange }}</p>
                <div class="flex flex-col items-center gap-3">
                    <img src="{{ asset('images/avatars/' . $roommate->avatar_slug . '.png') }}" alt="{{ $roommate->first_name }}" class="w-20 h-20 lg:w-24 lg:h-24 rounded-full object-cover">
                    <p class="text-lg lg:text-xl font-title font-bold text-center">C'est le tour de {{ $roommate->first_name }}</p>
                </div>
            @else
                <p class="text-muted-foreground text-center">Aucun coloc assigne pour le moment.</p>
            @endif
        </div>

        <a href="{{ route('coloc.dashboard', $coloc) }}" class="text-sm text-coral hover:underline font-title font-bold">
            Voir le planning complet →
        </a>
    </main>
</div>
@endsection
