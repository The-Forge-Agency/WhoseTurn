@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col">
    <x-header :coloc="$coloc" />

    <main class="flex-1 px-4 py-6 lg:py-10 max-w-md lg:max-w-4xl mx-auto w-full space-y-8">
        <div class="text-center space-y-1 print-header">
            <h1 class="text-xl lg:text-2xl font-bold font-title">QR Codes</h1>
            <p class="text-sm text-muted-foreground">{{ $coloc->name }}</p>
        </div>

        {{-- Coloc QR --}}
        <section class="space-y-4">
            <h2 class="text-lg font-bold font-title text-center">Lien de la coloc</h2>
            <div class="bg-card border border-border rounded-2xl p-6 lg:p-8 flex flex-col items-center gap-4 max-w-sm mx-auto print-qr-card">
                <img src="{{ route('coloc.qr.coloc', $coloc) }}" alt="QR Code coloc" class="w-48 h-48 lg:w-56 lg:h-56">
                <p class="text-sm text-muted-foreground text-center break-all">{{ route('coloc.dashboard', $coloc) }}</p>
                <a
                    href="{{ route('coloc.qr.coloc', $coloc) }}"
                    download="qr-{{ $coloc->name }}.svg"
                    class="text-sm text-coral hover:underline font-title font-bold print:hidden"
                >
                    Télécharger le QR
                </a>
            </div>
        </section>

        {{-- Task QRs --}}
        <section class="space-y-4">
            <h2 class="text-lg font-bold font-title text-center">QR par tâche</h2>
            <p class="text-xs text-muted-foreground text-center print:hidden">Scanne un QR pour voir qui doit faire la tâche cette semaine</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 lg:gap-4 print-grid">
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
        body {
            background: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        header, .print\:hidden, nav, footer {
            display: none !important;
        }

        main {
            padding: 10mm !important;
            max-width: 100% !important;
            margin: 0 !important;
        }

        .print-header {
            margin-bottom: 5mm;
        }

        .print-header h1 {
            font-size: 18pt !important;
        }

        .print-grid {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 4mm !important;
        }

        .print-qr-card {
            break-inside: avoid;
            border: 1px solid #ccc !important;
            background: white !important;
            padding: 3mm !important;
            border-radius: 4mm !important;
        }

        .print-qr-card img[alt^="QR"] {
            width: 25mm !important;
            height: 25mm !important;
        }

        .print-url {
            font-size: 6pt !important;
            color: #666 !important;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        section h2 {
            font-size: 12pt !important;
            margin-bottom: 3mm;
        }

        @page {
            size: A4;
            margin: 10mm;
        }
    }
</style>
@endpush
@endsection
