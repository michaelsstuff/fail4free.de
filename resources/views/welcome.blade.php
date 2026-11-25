@extends('layouts.guest')

@section('title', 'Willkommen')

@section('content')
    <div class="text-center space-y-5">
        <div class="space-y-2">
            <h1 class="text-xl font-semibold text-white">Private Community</h1>
            <p class="text-sm text-slate-400">
                Zugang nur per Einladung. Melde dich an, um Verbindungen und aktive Server zu sehen.
            </p>
        </div>

        <a href="{{ route('login') }}"
           class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
            Zum Login
        </a>
    </div>
@endsection
