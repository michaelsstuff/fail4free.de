@extends('layouts.guest')

@section('title', 'Serverfehler')

@section('content')
    <div class="text-center space-y-4">
        <div class="font-mono text-4xl font-bold text-amber-400">500</div>
        <h1 class="text-xl font-semibold text-white">Ein Fehler ist aufgetreten</h1>
        <p class="text-sm text-slate-400">Bitte versuche es gleich noch einmal oder gib einem Admin Bescheid.</p>
        <a href="{{ url('/') }}"
           class="inline-flex rounded-xl bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
            Zur Startseite
        </a>
    </div>
@endsection
