@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col">
    <x-header :coloc="$coloc" />

    <main class="flex-1 px-4 py-6 lg:py-10 max-w-md lg:max-w-3xl xl:max-w-4xl mx-auto w-full space-y-6 lg:space-y-8">
        <div class="flex items-center justify-between">
            <h1 class="text-xl lg:text-2xl font-bold font-title">Statistiques</h1>
            <a href="{{ route('coloc.dashboard', $coloc) }}" class="text-coral font-title font-bold text-sm hover:underline">← Retour</a>
        </div>

        <p class="text-xs text-muted-foreground text-center">Données des 4 dernières semaines</p>

        {{-- Roommate leaderboard --}}
        <section class="space-y-3">
            <h2 class="text-sm font-title font-bold">Classement des colocs</h2>
            @foreach($roommateStats->sortByDesc('rate') as $stat)
                @php $roommate = $stat['roommate']; @endphp
                <div class="bg-card border border-border rounded-2xl p-4 space-y-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ $roommate->avatar_url ? asset('storage/' . $roommate->avatar_url) : asset('images/avatars/' . $roommate->avatar_slug . '.png') }}" alt="{{ $roommate->first_name }}" class="w-10 h-10 rounded-full object-cover">
                        <div class="flex-1">
                            <p class="font-title font-bold text-ink text-sm">{{ $roommate->first_name }}</p>
                            <p class="text-xs text-muted-foreground">{{ $stat['total'] }} tâche(s) assignée(s)</p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-title font-bold {{ $stat['rate'] >= 75 ? 'text-green-500' : ($stat['rate'] >= 50 ? 'text-amber-500' : 'text-coral') }}">{{ $stat['rate'] }}%</p>
                        </div>
                    </div>

                    {{-- Progress bar --}}
                    <div class="w-full bg-muted rounded-full h-3 overflow-hidden">
                        @if($stat['total'] > 0)
                            <div class="h-full flex">
                                @if($stat['done'] > 0)
                                    <div class="bg-green-400 h-full" style="width: {{ ($stat['done'] / $stat['total']) * 100 }}%"></div>
                                @endif
                                @if($stat['done_by_other'] > 0)
                                    <div class="bg-amber-400 h-full" style="width: {{ ($stat['done_by_other'] / $stat['total']) * 100 }}%"></div>
                                @endif
                                @if($stat['not_done'] > 0)
                                    <div class="bg-coral h-full" style="width: {{ ($stat['not_done'] / $stat['total']) * 100 }}%"></div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="flex gap-4 text-[10px] text-muted-foreground">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-400"></span> Fait : {{ $stat['done'] }}</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400"></span> Par un autre : {{ $stat['done_by_other'] }}</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-coral"></span> Pas fait : {{ $stat['not_done'] }}</span>
                        @if($stat['helped_others'] > 0)
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-400"></span> A aidé : {{ $stat['helped_others'] }}x</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </section>

        {{-- Task distribution --}}
        <section class="space-y-3">
            <h2 class="text-sm font-title font-bold">Répartition par tâche</h2>
            @foreach($taskStats as $stat)
                <div class="bg-card border border-border rounded-2xl p-4 space-y-2">
                    <div class="flex items-center gap-3">
                        <img src="{{ $stat['task']->icon_src }}" alt="{{ $stat['task']->name }}" class="w-8 h-8 rounded-lg object-cover">
                        <p class="font-title font-bold text-ink text-sm flex-1">{{ $stat['task']->name }}</p>
                        <span class="text-xs text-muted-foreground">{{ $stat['done'] }}/{{ $stat['total'] }} fait</span>
                    </div>

                    @if($stat['total'] > 0)
                        <div class="flex gap-1">
                            @foreach($roommates as $roommate)
                                @php $count = $stat['per_roommate'][$roommate->id] ?? 0; @endphp
                                @if($count > 0)
                                    <div class="flex items-center gap-1 bg-cream rounded-lg px-2 py-1">
                                        <img src="{{ $roommate->avatar_url ? asset('storage/' . $roommate->avatar_url) : asset('images/avatars/' . $roommate->avatar_slug . '.png') }}" alt="{{ $roommate->first_name }}" class="w-5 h-5 rounded-full object-cover">
                                        <span class="text-[10px] text-ink font-title font-bold">{{ $count }}x</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-muted-foreground">Pas encore de données</p>
                    @endif
                </div>
            @endforeach
        </section>

        {{-- Todo stats --}}
        @if($todosTotal > 0)
            <section class="bg-card border border-border rounded-2xl p-4 space-y-2">
                <h2 class="text-sm font-title font-bold">Tâches urgentes</h2>
                <div class="flex items-center gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-title font-bold text-ink">{{ $todosTotal }}</p>
                        <p class="text-[10px] text-muted-foreground">créées</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-title font-bold text-green-500">{{ $todosDone }}</p>
                        <p class="text-[10px] text-muted-foreground">terminées</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-title font-bold text-coral">{{ $todosTotal - $todosDone }}</p>
                        <p class="text-[10px] text-muted-foreground">en attente</p>
                    </div>
                </div>
            </section>
        @endif
    </main>
</div>
@endsection
