@extends('layouts.app')

@section('title', 'Start')

@section('content')
    <div class="space-y-10">
        {{-- Verbindungen --}}
        <section>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white">Verbindungen</h1>
                    <p class="mt-1 text-sm text-slate-400">TeamSpeak, Discord und weitere Community-Links.</p>
                </div>
                @if(auth()->user()?->is_admin)
                    <a href="{{ route('admin.links.index') }}" class="text-xs font-medium text-indigo-400 hover:text-indigo-300">
                        Verwalten &rarr;
                    </a>
                @endif
            </div>

            @if($links->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-800 bg-slate-900/40 p-8 text-center text-sm text-slate-500">
                    Noch keine Verbindungen eingetragen.
                </div>
            @else
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($links as $link)
                        <div x-data="{ copied: false }"
                             class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 flex flex-col justify-between gap-4">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset
                                        @if($link->type === 'teamspeak') bg-sky-500/10 text-sky-300 ring-sky-500/30
                                        @elseif($link->type === 'discord') bg-indigo-500/10 text-indigo-300 ring-indigo-500/30
                                        @else bg-slate-700/40 text-slate-300 ring-slate-600/40 @endif">
                                        {{ $link->typeLabel() }}
                                    </span>
                                </div>

                                <h2 class="text-base font-semibold text-white">{{ $link->label }}</h2>

                                <div class="rounded-xl bg-slate-950/80 border border-slate-800/80 px-3 py-2 font-mono text-xs text-slate-200 break-all">
                                    {{ $link->value }}
                                </div>

                                @if($link->note)
                                    <p class="text-xs text-slate-400 leading-relaxed">{{ $link->note }}</p>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 pt-1">
                                @if($link->isClickableUrl())
                                    <a href="{{ $link->value }}" target="_blank" rel="noopener noreferrer"
                                       class="flex-1 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-indigo-500">
                                        Öffnen
                                    </a>
                                @endif

                                <button type="button"
                                        @click="navigator.clipboard.writeText(@js($link->value)); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="flex-1 inline-flex items-center justify-center rounded-xl border border-slate-700 bg-slate-800/70 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-slate-800 cursor-pointer">
                                    <span x-show="!copied">Kopieren</span>
                                    <span x-show="copied" x-cloak class="text-emerald-400">Kopiert!</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Dedicated Server --}}
        <section>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Dedicated Server</h2>
                    <p class="mt-1 text-sm text-slate-400">Aktuell laufende Server, gruppiert nach Spiel.</p>
                </div>
                @if(auth()->user()?->is_admin)
                    <a href="{{ route('admin.servers.index') }}" class="text-xs font-medium text-indigo-400 hover:text-indigo-300">
                        Verwalten &rarr;
                    </a>
                @endif
            </div>

            @if($serversByGame->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-800 bg-slate-900/40 p-8 text-center text-sm text-slate-500">
                    Aktuell sind keine aktiven Dedicated Server eingetragen.
                </div>
            @else
                <div class="space-y-8">
                    @foreach($serversByGame as $game => $servers)
                        <div class="space-y-3">
                            <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                {{ $game }}
                            </h3>

                            <div class="grid gap-4 md:grid-cols-2">
                                @foreach($servers as $server)
                                    <div x-data="{ copiedAddr: false, showPw: false, copiedPw: false }"
                                         class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 space-y-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <h4 class="text-base font-semibold text-white">{{ $server->name }}</h4>
                                                @if($server->description)
                                                    <p class="mt-1 text-sm text-slate-400">{{ $server->description }}</p>
                                                @endif
                                            </div>
                                            <span class="shrink-0 inline-flex items-center rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-medium text-emerald-300 ring-1 ring-inset ring-emerald-500/30">
                                                Aktiv
                                            </span>
                                        </div>

                                        <div class="space-y-2.5 text-sm">
                                            <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-800 bg-slate-950/80 px-3.5 py-2.5">
                                                <div class="min-w-0">
                                                    <div class="text-[11px] uppercase tracking-wider text-slate-500">Adresse</div>
                                                    <div class="font-mono text-xs text-slate-100 truncate">{{ $server->connectionAddress() }}</div>
                                                </div>
                                                <button type="button"
                                                        @click="navigator.clipboard.writeText(@js($server->connectionAddress())); copiedAddr = true; setTimeout(() => copiedAddr = false, 2000)"
                                                        class="shrink-0 rounded-lg border border-slate-700 bg-slate-800/80 px-2.5 py-1.5 text-xs font-medium text-slate-200 hover:bg-slate-700 cursor-pointer">
                                                    <span x-show="!copiedAddr">Kopieren</span>
                                                    <span x-show="copiedAddr" x-cloak class="text-emerald-400">Kopiert!</span>
                                                </button>
                                            </div>

                                            @if($server->password !== null && $server->password !== '')
                                                <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-800 bg-slate-950/80 px-3.5 py-2.5">
                                                    <div class="min-w-0">
                                                        <div class="text-[11px] uppercase tracking-wider text-slate-500">Passwort</div>
                                                        <div class="font-mono text-xs text-slate-100">
                                                            <span x-show="!showPw">&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</span>
                                                            <span x-show="showPw" x-cloak>{{ $server->password }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="shrink-0 flex items-center gap-1.5">
                                                        <button type="button" @click="showPw = !showPw"
                                                                class="rounded-lg border border-slate-700 bg-slate-800/80 px-2.5 py-1.5 text-xs font-medium text-slate-200 hover:bg-slate-700 cursor-pointer">
                                                            <span x-show="!showPw">Anzeigen</span>
                                                            <span x-show="showPw" x-cloak>Verbergen</span>
                                                        </button>
                                                        <button type="button"
                                                                @click="navigator.clipboard.writeText(@js($server->password)); copiedPw = true; setTimeout(() => copiedPw = false, 2000)"
                                                                class="rounded-lg border border-slate-700 bg-slate-800/80 px-2.5 py-1.5 text-xs font-medium text-slate-200 hover:bg-slate-700 cursor-pointer">
                                                            <span x-show="!copiedPw">Kopieren</span>
                                                            <span x-show="copiedPw" x-cloak class="text-emerald-400">Kopiert!</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        @if($server->notes)
                                            <div class="rounded-xl bg-slate-950/50 border border-slate-800/60 px-3.5 py-2.5 text-xs text-slate-300 whitespace-pre-line">
                                                {{ $server->notes }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
