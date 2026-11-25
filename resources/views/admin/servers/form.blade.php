@extends('layouts.app')

@section('title', $server->exists ? 'Server bearbeiten' : 'Neuer Server')

@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-white">
                {{ $server->exists ? 'Server bearbeiten' : 'Neuer Server' }}
            </h1>
            <a href="{{ route('admin.servers.index') }}" class="text-sm text-slate-400 hover:text-white">&larr; Zurück</a>
        </div>

        <form method="POST"
              action="{{ $server->exists ? route('admin.servers.update', $server) : route('admin.servers.store') }}"
              class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 space-y-5">
            @csrf
            @if($server->exists)
                @method('PUT')
            @endif

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="game" class="block text-sm font-medium text-slate-300 mb-1.5">Spiel</label>
                    <input id="game" name="game" type="text" value="{{ old('game', $server->game) }}" required placeholder="z. B. Valheim, Factorio, Minecraft"
                           class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-300 mb-1.5">Servername</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $server->name) }}" required placeholder="z. B. Hauptwelt"
                           class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
                <div class="sm:col-span-2">
                    <label for="host" class="block text-sm font-medium text-slate-300 mb-1.5">Host / IP</label>
                    <input id="host" name="host" type="text" value="{{ old('host', $server->host) }}" required placeholder="games.fail4free.de"
                           class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm font-mono text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>

                <div>
                    <label for="port" class="block text-sm font-medium text-slate-300 mb-1.5">Port (optional)</label>
                    <input id="port" name="port" type="number" min="1" max="65535" value="{{ old('port', $server->port) }}" placeholder="2456"
                           class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm font-mono text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">
                    Server-Passwort (optional, wird verschlüsselt gespeichert)
                </label>
                <input id="password" name="password" type="text" value="{{ old('password') }}"
                       placeholder="{{ $server->exists && $server->password ? 'Leer lassen, um bestehendes Passwort zu behalten' : 'Optionales Server-Passwort' }}"
                       class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm font-mono text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">

                @if($server->exists && $server->password)
                    <label class="mt-2 inline-flex items-center gap-2 text-xs text-slate-400">
                        <input type="checkbox" name="clear_password" value="1" class="rounded border-slate-700 bg-slate-950 text-rose-600">
                        Gespeichertes Passwort entfernen
                    </label>
                @endif
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-slate-300 mb-1.5">Kurzbeschreibung (optional)</label>
                <input id="description" name="description" type="text" value="{{ old('description', $server->description) }}"
                       class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-slate-300 mb-1.5">Notizen / Mods / Hinweise (optional)</label>
                <textarea id="notes" name="notes" rows="4"
                          class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">{{ old('notes', $server->notes) }}</textarea>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 items-center">
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-slate-300 mb-1.5">Reihenfolge</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $server->sort_order ?? 0) }}"
                           class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>

                <label class="flex items-center gap-2.5 text-sm text-slate-300 sm:mt-6">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $server->is_active ?? true))
                           class="h-4 w-4 rounded border-slate-700 bg-slate-950 text-indigo-600 focus:ring-indigo-500">
                    Auf dem Dashboard anzeigen (aktiv)
                </label>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 cursor-pointer">
                    Speichern
                </button>
                <a href="{{ route('admin.servers.index') }}"
                   class="rounded-xl border border-slate-700 px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-800">
                    Abbrechen
                </a>
            </div>
        </form>
    </div>
@endsection
