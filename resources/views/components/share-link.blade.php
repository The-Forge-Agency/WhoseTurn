@props(['coloc'])

<div class="space-y-2">
    <p class="text-xs text-muted-foreground text-center">Partage ce lien a tes colocs :</p>
    <div
        x-data="{ copied: false }"
        class="flex items-center rounded-2xl bg-card p-3 border border-border gap-3"
    >
        <img src="{{ route('coloc.qr.coloc', $coloc) }}" alt="QR" class="w-10 h-10 shrink-0">
        <span class="flex-1 text-sm text-muted-foreground truncate">{{ url(route('coloc.dashboard', $coloc, false)) }}</span>
        <button
            type="button"
            @click="navigator.clipboard.writeText('{{ url(route('coloc.dashboard', $coloc, false)) }}'); copied = true; setTimeout(() => copied = false, 2000)"
            class="bg-coral hover:bg-coral-dark text-cream rounded-xl px-3 py-1.5 text-sm font-title font-bold transition-colors shrink-0"
        >
            <span x-show="!copied">Copier</span>
            <span x-show="copied" x-cloak>Copie !</span>
        </button>
    </div>
</div>
