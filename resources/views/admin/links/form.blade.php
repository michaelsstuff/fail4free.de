@extends('layouts.app')

@section('title', $link->exists ? 'Verbindung bearbeiten' : 'Neue Verbindung')

@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-white">
                {{ $link->exists ? 'Verbindung bearbeiten' : 'Neue Verbindung' }}
            </h1>
            <a href="{{ route('admin.links.index') }}" class="text-sm text-slate-400 hover:text-white">&larr; Zurück</a>
        </div>

        <form method="POST"
              action="{{ $link->exists ? route('admin.links.update', $link) : route('admin.links.store') }}"
              class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 space-y-5">
            @csrf
            @if($link->exists)
                @method('PUT')
            @endif

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="type" class="block text-sm font-medium text-slate-300 mb-1.5">Typ</label>
                    <select id="type" name="type" required
                            class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" @selected(old('type', $link->type) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="sort_order" class="block text-sm font-medium text-slate-300 mb-1.5">Reihenfolge</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $link->sort_order ?? 0) }}"
                           class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>
            </div>

            <div>
                <label for="label" class="block text-sm font-medium text-slate-300 mb-1.5">Bezeichnung</label>
                <input id="label" name="label" type="text" value="{{ old('label', $link->label) }}" required placeholder="z. B. Haupt-TeamSpeak"
                       class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
            </div>

            <div>
                <label for="value" class="block text-sm font-medium text-slate-300 mb-1.5">Adresse / Invite-URL</label>
                <input id="value" name="value" type="text" value="{{ old('value', $link->value) }}" required placeholder="ts.fail4free.de oder https://discord.gg/..."
                       class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm font-mono text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
            </div>

            <div>
                <label for="note" class="block text-sm font-medium text-slate-300 mb-1.5">Hinweis (optional)</label>
                <textarea id="note" name="note" rows="3"
                          class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">{{ old('note', $link->note) }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 cursor-pointer">
                    Speichern
                </button>
                <a href="{{ route('admin.links.index') }}"
                   class="rounded-xl border border-slate-700 px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-800">
                    Abbrechen
                </a>
            </div>
        </form>
    </div>
@endsection
