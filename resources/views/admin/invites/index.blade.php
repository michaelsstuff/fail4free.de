@extends('layouts.app')

@section('title', 'Admin — Einladungen')

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Einladungen</h1>
            <p class="mt-1 text-sm text-slate-400">Neue Mitglieder per E-Mail einladen, offene Einladungen erneut senden oder widerrufen.</p>
        </div>

        <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6">
            <h2 class="text-base font-semibold text-white mb-4">Neue Einladung versenden</h2>
            <form method="POST" action="{{ route('admin.invites.store') }}" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="freund@beispiel.de"
                       class="flex-1 rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                <button type="submit"
                        class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 cursor-pointer">
                    Einladung senden
                </button>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-800 bg-slate-900/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead class="bg-slate-950/60 text-left text-xs uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-5 py-3.5">E-Mail</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5">Eingeladen von</th>
                            <th class="px-5 py-3.5">Gültig bis</th>
                            <th class="px-5 py-3.5 text-right">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($invites as $invite)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-5 py-3.5 font-medium text-white">{{ $invite->email }}</td>
                                <td class="px-5 py-3.5">
                                    @if($invite->isAccepted())
                                        <span class="inline-flex rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-medium text-emerald-300 ring-1 ring-inset ring-emerald-500/30">
                                            Angenommen ({{ $invite->accepted_at->format('d.m.Y') }})
                                        </span>
                                    @elseif($invite->isExpired())
                                        <span class="inline-flex rounded-full bg-amber-500/10 px-2.5 py-0.5 text-xs font-medium text-amber-300 ring-1 ring-inset ring-amber-500/30">
                                            Abgelaufen
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-indigo-500/10 px-2.5 py-0.5 text-xs font-medium text-indigo-300 ring-1 ring-inset ring-indigo-500/30">
                                            Offen
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-slate-400">{{ $invite->inviter?->name ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-slate-400">{{ $invite->expires_at->format('d.m.Y H:i') }}</td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    @if(! $invite->isAccepted())
                                        <div class="inline-flex items-center gap-2">
                                            <form method="POST" action="{{ route('admin.invites.resend', $invite) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="rounded-lg border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-medium text-slate-200 hover:bg-slate-700 cursor-pointer">
                                                    Erneut senden
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.invites.destroy', $invite) }}"
                                                  onsubmit="return confirm('Einladung für {{ $invite->email }} wirklich widerrufen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="rounded-lg border border-rose-500/30 bg-rose-500/10 px-3 py-1.5 text-xs font-medium text-rose-300 hover:bg-rose-500/20 cursor-pointer">
                                                    Widerrufen
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-600">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-500">Noch keine Einladungen vorhanden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
