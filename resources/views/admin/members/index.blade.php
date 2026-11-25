@extends('layouts.app')

@section('title', 'Admin — Mitglieder')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Mitglieder</h1>
            <p class="mt-1 text-sm text-slate-400">Admin-Rechte vergeben und Konten aktivieren oder deaktivieren.</p>
        </div>

        <section class="rounded-2xl border border-slate-800 bg-slate-900/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead class="bg-slate-950/60 text-left text-xs uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-5 py-3.5">Name</th>
                            <th class="px-5 py-3.5">E-Mail</th>
                            <th class="px-5 py-3.5">Rolle</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5">Eingeladen von</th>
                            <th class="px-5 py-3.5 text-right">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @foreach($members as $member)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-5 py-3.5 font-medium text-white">
                                    {{ $member->name }}
                                    @if($member->is(auth()->user()))
                                        <span class="ml-1 text-xs text-slate-500">(du)</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-slate-300">{{ $member->email }}</td>
                                <td class="px-5 py-3.5">
                                    @if($member->is_admin)
                                        <span class="inline-flex rounded-full bg-indigo-500/10 px-2.5 py-0.5 text-xs font-medium text-indigo-300 ring-1 ring-inset ring-indigo-500/30">Admin</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-700/40 px-2.5 py-0.5 text-xs font-medium text-slate-300 ring-1 ring-inset ring-slate-600/40">Mitglied</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($member->is_active)
                                        <span class="inline-flex rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-medium text-emerald-300 ring-1 ring-inset ring-emerald-500/30">Aktiv</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-rose-500/10 px-2.5 py-0.5 text-xs font-medium text-rose-300 ring-1 ring-inset ring-rose-500/30">Deaktiviert</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-slate-400">{{ $member->inviter?->name ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <form method="POST" action="{{ route('admin.members.toggle-admin', $member) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="rounded-lg border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-medium text-slate-200 hover:bg-slate-700 cursor-pointer">
                                                {{ $member->is_admin ? 'Admin entziehen' : 'Zum Admin machen' }}
                                            </button>
                                        </form>

                                        @if(! $member->is(auth()->user()))
                                            <form method="POST" action="{{ route('admin.members.toggle-active', $member) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="rounded-lg px-3 py-1.5 text-xs font-medium cursor-pointer {{ $member->is_active ? 'border border-rose-500/30 bg-rose-500/10 text-rose-300 hover:bg-rose-500/20' : 'border border-emerald-500/30 bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20' }}">
                                                    {{ $member->is_active ? 'Deaktivieren' : 'Aktivieren' }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
