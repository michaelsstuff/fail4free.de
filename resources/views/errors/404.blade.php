@extends('layouts.guest')

@section('title', 'Nicht gefunden')

@section('content')
    <div class="text-center space-y-4">
        <div class="font-mono text-4xl font-bold text-indigo-400">404</div>
        <h1 class="text-xl font-semibold text-white">Seite nicht gefunden</h1>
        <p class="text-sm text-slate-400">Die angeforderte Adresse existiert nicht.</p>
        <a href="{{ auth()->check() ? route('dashboard') : route('home') }}"
           class="inline-flex rounded-xl bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
            Zurück zur Startseite
        </a>
    </div>
@endsection
