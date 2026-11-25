@extends('layouts.app')

@section('title', 'Admin — Verbindungen')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Verbindungen</h1>
                <p class="mt-1 text-sm text-slate-400">TeamSpeak, Discord und sonstige Links für das Dashboard.</p>
            </div>
            <a href="{{ route('admin.links.create') }}"
               class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500">
                + Neue Verbindung
            </a>
        </div>

        <section class="rounded-2xl border border-slate-800 bg-slate-900/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead class="bg-slate-950/60 text-left text-xs uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-5 py-3.5">Reihenfolge</th>
                            <th class="px-5 py-3.5">Typ</th>
                            <th class="px-5 py-3.5">Bezeichnung</th>
                            <th class="px-5 py-3.5">Adresse / URL</th>
                            <th class="px-5 py-3.5 text-right">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($links as $link)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-5 py-3.5 font-mono text-slate-400">{{ $link->sort_order }}</td>
                                <td class="px-5 py-3.5 text-slate-300">{{ $link->typeLabel() }}</td>
                                <td class="px-5 py-3.5 font-medium text-white">{{ $link->label }}</td>
                                <td class="px-5 py-3.5 font-mono text-xs text-slate-300 max-w-xs truncate">{{ $link->value }}</td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.links.edit', $link) }}"
                                           class="rounded-lg border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-medium text-slate-200 hover:bg-slate-700">
                                            Bearbeiten
                                        </a>
                                        <form method="POST" action="{{ route('admin.links.destroy', $link) }}"
                                              onsubmit="return confirm('Verbindung „{{ $link->label }}“ wirklich löschen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-lg border border-rose-500/30 bg-rose-500/10 px-3 py-1.5 text-xs font-medium text-rose-300 hover:bg-rose-500/20 cursor-pointer">
                                                Löschen
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-500">Noch keine Verbindungen vorhanden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
