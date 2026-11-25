@extends('layouts.app')

@section('title', 'Admin — Server')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Dedicated Server</h1>
                <p class="mt-1 text-sm text-slate-400">Aktive und pausierte Game-Server verwalten.</p>
            </div>
            <a href="{{ route('admin.servers.create') }}"
               class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500">
                + Neuer Server
            </a>
        </div>

        <section class="rounded-2xl border border-slate-800 bg-slate-900/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead class="bg-slate-950/60 text-left text-xs uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-5 py-3.5">Spiel</th>
                            <th class="px-5 py-3.5">Name</th>
                            <th class="px-5 py-3.5">Adresse</th>
                            <th class="px-5 py-3.5">Passwort</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($servers as $server)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-5 py-3.5 text-slate-300">{{ $server->game }}</td>
                                <td class="px-5 py-3.5 font-medium text-white">{{ $server->name }}</td>
                                <td class="px-5 py-3.5 font-mono text-xs text-slate-300">{{ $server->connectionAddress() }}</td>
                                <td class="px-5 py-3.5 text-xs text-slate-400">
                                    {{ $server->password ? 'Gesetzt (verschlüsselt)' : 'Keines' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($server->is_active)
                                        <span class="inline-flex rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-medium text-emerald-300 ring-1 ring-inset ring-emerald-500/30">Aktiv</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-700/40 px-2.5 py-0.5 text-xs font-medium text-slate-400 ring-1 ring-inset ring-slate-600/40">Inaktiv</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.servers.edit', $server) }}"
                                           class="rounded-lg border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-medium text-slate-200 hover:bg-slate-700">
                                            Bearbeiten
                                        </a>
                                        <form method="POST" action="{{ route('admin.servers.destroy', $server) }}"
                                              onsubmit="return confirm('Server „{{ $server->name }}“ wirklich löschen?')">
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
                                <td colspan="6" class="px-5 py-8 text-center text-slate-500">Noch keine Server angelegt.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
