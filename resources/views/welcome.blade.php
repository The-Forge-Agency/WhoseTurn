@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen px-4 py-8">
    <img src="{{ asset('images/icon.svg') }}" alt="WhoseTurn" class="w-18 h-18 mb-4">
    <h1 class="text-3xl font-bold font-title text-ink">WhoseTurn</h1>
    <p class="text-sm text-muted-foreground mt-2 font-body">Fondation OK</p>
</div>
@endsection
