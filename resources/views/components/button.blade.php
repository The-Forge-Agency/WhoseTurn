@props(['variant' => 'primary'])

@php
$classes = match($variant) {
    'primary' => 'bg-coral hover:bg-coral-dark text-cream',
    'secondary' => 'border border-border text-ink hover:bg-muted',
    'dashed' => 'border-2 border-dashed border-coral text-coral hover:bg-coral/10',
};
@endphp

<button {{ $attributes->merge(['type' => 'submit', 'class' => "$classes font-title font-bold rounded-2xl py-3 px-6 w-full transition-colors disabled:opacity-40 disabled:cursor-not-allowed"]) }}>
    {{ $slot }}
</button>
