@extends('layouts.guest')

@section('title', 'Einladung annehmen')

@section('content')
    <h1 class="text-xl font-semibold text-white mb-2">Konto erstellen</h1>
    <p class="text-sm text-slate-400 mb-6">
        Du wurdest zu fail4free.de eingeladen. Wähle deinen Namen und ein sicheres Passwort.
    </p>

    <form method="POST" action="{{ route('invites.accept.store', ['token' => $token]) }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">E-Mail-Adresse</label>
            <input id="email" type="email" value="{{ $invite->email }}" disabled
                   class="w-full rounded-xl border border-slate-800 bg-slate-950/40 px-3.5 py-2.5 text-sm text-slate-400 cursor-not-allowed">
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-slate-300 mb-1.5">Anzeigename</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
            @error('name')
                <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">Passwort (mind. 10 Zeichen)</label>
            <input id="password" name="password" type="password" required autocomplete="new-password"
                   class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
            @error('password')
                <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-1.5">Passwort bestätigen</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                   class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
        </div>

        <button type="submit"
                class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 cursor-pointer">
            Registrierung abschließen
        </button>
    </form>
@endsection
