@extends('layouts.guest')

@section('title', 'Einladung ungültig')

@section('content')
    <div class="text-center space-y-5">
        <div class="space-y-2">
            <h1 class="text-xl font-semibold text-white">Einladung nicht gültig</h1>
            <p class="text-sm text-slate-400">{{ $reason }}</p>
        </div>

        <a href="{{ route('login') }}"
           class="inline-flex w-full items-center justify-center rounded-xl border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700">
            Zum Login
        </a>
    </div>
@endsection
