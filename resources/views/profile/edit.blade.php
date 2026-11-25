@extends('layouts.app')

@section('title', 'Profil')

@section('content')
    <div class="max-w-2xl space-y-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Profil</h1>
            <p class="mt-1 text-sm text-slate-400">Verwalte deinen Namen, deine E-Mail-Adresse und dein Passwort.</p>
        </div>

        <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 class="text-base font-semibold text-white mb-4">Kontodaten</h2>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-300 mb-1.5">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                           class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">E-Mail-Adresse</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                           class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>

                <div class="pt-1">
                    <button type="submit"
                            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 cursor-pointer">
                        Speichern
                    </button>
                </div>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 class="text-base font-semibold text-white mb-4">Passwort ändern</h2>

            @if(session('password_status'))
                <div class="mb-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                    {{ session('password_status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-medium text-slate-300 mb-1.5">Aktuelles Passwort</label>
                    <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                           class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">Neues Passwort (mind. 10 Zeichen)</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                           class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-1.5">Neues Passwort bestätigen</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                           class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>

                <div class="pt-1">
                    <button type="submit"
                            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 cursor-pointer">
                        Passwort aktualisieren
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
