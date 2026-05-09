@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col">
    <x-header :coloc="$coloc" />

    <main class="flex-1 px-4 py-6 lg:py-10 max-w-md lg:max-w-4xl mx-auto w-full space-y-8">
        <div class="text-center space-y-1">
            <h1 class="text-xl lg:text-2xl font-bold font-title">QR Codes</h1>
            <p class="text-sm text-muted-foreground">{{ $coloc->name }}</p>
        </div>

        {{-- Coloc QR --}}
        <section class="space-y-4">
            <h2 class="text-lg font-bold font-title text-center">Lien de la coloc</h2>
            <div class="bg-card border border-border rounded-2xl p-6 lg:p-8 flex flex-col items-center gap-4 max-w-sm mx-auto">
                <img src="{{ route('coloc.qr.coloc', $coloc) }}" alt="QR Code coloc" class="w-48 h-48 lg:w-56 lg:h-56">
                <p class="text-sm text-muted-foreground text-center break-all">{{ route('coloc.dashboard', $coloc) }}</p>
            </div>
        </section>

        {{-- Task QRs --}}
        <section class="space-y-4">
            <h2 class="text-lg font-bold font-title text-center">QR par tache</h2>
            <p class="text-xs text-muted-foreground text-center">Scanne un QR pour voir qui doit faire la tâche cette semaine</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 lg:gap-4">
                @foreach($tasks as $task)
                    <x-qr-card :coloc="$coloc" :task="$task" />
                @endforeach
            </div>
        </section>

        <div class="pt-4 print:hidden flex flex-col sm:flex-row items-center gap-3 max-w-md mx-auto w-full">
            <button
                type="button"
                onclick="window.print()"
                class="w-full sm:flex-1 bg-coral hover:bg-coral-dark text-cream rounded-2xl py-3 px-6 font-title font-bold transition-colors"
            >
                Imprimer cette page
            </button>
            <a href="{{ route('coloc.dashboard', $coloc) }}" class="text-sm text-coral hover:underline font-title font-bold">
                ← Retour au dashboard
            </a>
        </div>
    </main>
</div>

@push('scripts')
<style>
    @media print {
        body { background: white !important; }
        header, .print\:hidden { display: none !important; }
        main { padding: 0 !important; max-width: 100% !important; }
        .bg-card { border: 1px solid #ccc !important; break-inside: avoid; }
    }
</style>
@endpush
@endsection
