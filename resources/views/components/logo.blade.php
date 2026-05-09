@props(['type' => 'icon', 'class' => ''])

@if($type === 'full')
    <img src="{{ asset('images/logo.svg') }}" alt="WhoseTurn" {{ $attributes->merge(['class' => $class]) }}>
@else
    <img src="{{ asset('images/icon.svg') }}" alt="WhoseTurn" {{ $attributes->merge(['class' => $class]) }}>
@endif
