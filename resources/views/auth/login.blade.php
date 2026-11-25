@extends('layouts.guest')

@section('title', 'Anmelden')

@section('content')
    <h1 class="text-xl font-semibold text-white mb-6">Anmelden</h1>

    <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">E-Mail-Adresse</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
            @error('email')
                <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-medium text-slate-300">Passwort</label>
                <a href="{{ route('password.request') }}" class="text-xs text-indigo-400 hover:text-indigo-300">
                    Passwort vergessen?
                </a>
            </div>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                   class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
            @error('password')
                <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-2.5 text-sm text-slate-300 select-none">
            <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-slate-700 bg-slate-950 text-indigo-600 focus:ring-indigo-500">
            Angemeldet bleiben
        </label>

        <button type="submit"
                class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 cursor-pointer">
            Anmelden
        </button>
    </form>

    <p class="mt-6 text-center text-xs text-slate-500">
        Kein Konto? Registrierung ist nur über einen Einladungslink möglich.
    </p>
@endsection
