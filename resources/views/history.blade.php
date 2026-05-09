@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col">
    <x-header :coloc="$coloc" />

    <main class="flex-1 px-4 py-6 lg:py-10 max-w-md lg:max-w-3xl xl:max-w-4xl mx-auto w-full space-y-6 lg:space-y-8">
        <div class="flex items-center justify-between">
            <h1 class="text-xl lg:text-2xl font-bold font-title">Historique</h1>
            <a href="{{ route('coloc.dashboard', $coloc) }}" class="text-coral font-title font-bold text-sm hover:underline">← Retour</a>
        </div>

        @foreach($weeks as $weekData)
            <section class="space-y-3">
                <h2 class="text-sm font-title font-bold text-ink flex items-center gap-2">
                    {{ $weekData['label'] }}
                    @if($weekData['isCurrent'])
                        <span class="text-[10px] bg-coral/10 text-coral px-2 py-0.5 rounded-full font-body">en cours</span>
                    @endif
                </h2>

                @if($weekData['assignments']->isEmpty())
                    <p class="text-xs text-muted-foreground py-2">Aucune tâche cette semaine</p>
                @else
                    <div class="space-y-2">
                        @foreach($weekData['assignments'] as $assignment)
                            @php
                                $completion = $weekData['completions']->get($assignment['task']->id);
                                $status = $completion?->status;
                                $borderClass = match($status) {
                                    'done' => 'border-green-300 bg-green-50/50',
                                    'not_done' => 'border-coral/50 bg-coral/5',
                                    'done_by_other' => 'border-amber-300 bg-amber-50/50',
                                    default => 'border-border bg-card',
                                };
                            @endphp
                            <div class="flex items-center gap-3 rounded-2xl border p-3 {{ $borderClass }}">
                                <img src="{{ $assignment['task']->icon_src }}" alt="{{ $assignment['task']->name }}" class="w-8 h-8 rounded-lg object-cover shrink-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-title font-bold text-ink truncate">{{ $assignment['task']->name }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ $assignment['roommate']->first_name }}
                                        @if($status === 'done') — Fait !
                                        @elseif($status === 'not_done') — Pas fait
                                        @elseif($status === 'done_by_other') — Fait par {{ $completion->actualRoommate?->first_name ?? 'quelqu\'un' }}
                                        @elseif(!$weekData['isCurrent']) — Non renseigné
                                        @endif
                                    </p>
                                </div>
                                @if($status)
                                    <span class="text-lg shrink-0
                                        @if($status === 'done') text-green-400
                                        @elseif($status === 'not_done') text-coral
                                        @else text-amber-400
                                        @endif
                                    ">
                                        @if($status === 'done') ✓ @elseif($status === 'not_done') ✗ @else ↺ @endif
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        @endforeach
    </main>
</div>
@endsection
