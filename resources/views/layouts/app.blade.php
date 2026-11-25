<!DOCTYPE html>
<html lang="de" class="h-full bg-slate-950 text-slate-100 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'fail4free.de') — fail4free.de</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col">
    <header x-data="{ open: false }" class="border-b border-slate-800/80 bg-slate-900/80 backdrop-blur sticky top-0 z-30">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 font-semibold tracking-tight text-white">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500/20 text-indigo-400 ring-1 ring-inset ring-indigo-500/40 font-mono text-sm font-bold">f4f</span>
                        <span class="text-base">fail4free.de</span>
                    </a>

                    <nav class="hidden md:flex items-center gap-1 text-sm">
                        <a href="{{ route('dashboard') }}"
                           class="rounded-lg px-3 py-2 font-medium transition {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            Start
                        </a>

                        @if(auth()->user()?->is_admin)
                            <span class="mx-1 h-4 w-px bg-slate-800"></span>
                            <a href="{{ route('admin.servers.index') }}"
                               class="rounded-lg px-3 py-2 font-medium transition {{ request()->routeIs('admin.servers.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                                Server
                            </a>
                            <a href="{{ route('admin.links.index') }}"
                               class="rounded-lg px-3 py-2 font-medium transition {{ request()->routeIs('admin.links.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                                Verbindungen
                            </a>
                            <a href="{{ route('admin.invites.index') }}"
                               class="rounded-lg px-3 py-2 font-medium transition {{ request()->routeIs('admin.invites.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                                Einladungen
                            </a>
                            <a href="{{ route('admin.members.index') }}"
                               class="rounded-lg px-3 py-2 font-medium transition {{ request()->routeIs('admin.members.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                                Mitglieder
                            </a>
                        @endif
                    </nav>
                </div>

                <div class="hidden md:flex items-center gap-3 text-sm">
                    <a href="{{ route('profile.edit') }}"
                       class="rounded-lg px-3 py-2 font-medium transition {{ request()->routeIs('profile.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        {{ auth()->user()->name }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="rounded-lg border border-slate-700 bg-slate-800/60 px-3 py-1.5 text-slate-300 transition hover:border-slate-600 hover:bg-slate-800 hover:text-white cursor-pointer">
                            Abmelden
                        </button>
                    </form>
                </div>

                <button type="button" @click="open = !open"
                        class="md:hidden inline-flex items-center justify-center rounded-lg p-2 text-slate-300 hover:bg-slate-800 hover:text-white cursor-pointer"
                        aria-label="Menü umschalten">
                    <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div x-show="open" x-cloak class="md:hidden border-t border-slate-800 bg-slate-900 px-4 py-3 space-y-1 text-sm">
            <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-slate-200 hover:bg-slate-800">Start</a>
            @if(auth()->user()?->is_admin)
                <div class="pt-2 pb-1 px-3 text-xs font-semibold uppercase tracking-wider text-slate-500">Admin</div>
                <a href="{{ route('admin.servers.index') }}" class="block rounded-lg px-3 py-2 text-slate-200 hover:bg-slate-800">Server</a>
                <a href="{{ route('admin.links.index') }}" class="block rounded-lg px-3 py-2 text-slate-200 hover:bg-slate-800">Verbindungen</a>
                <a href="{{ route('admin.invites.index') }}" class="block rounded-lg px-3 py-2 text-slate-200 hover:bg-slate-800">Einladungen</a>
                <a href="{{ route('admin.members.index') }}" class="block rounded-lg px-3 py-2 text-slate-200 hover:bg-slate-800">Mitglieder</a>
            @endif
            <div class="border-t border-slate-800 my-2 pt-2">
                <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-slate-200 hover:bg-slate-800">Profil ({{ auth()->user()->name }})</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="w-full text-left rounded-lg px-3 py-2 text-rose-400 hover:bg-slate-800 cursor-pointer">
                        Abmelden
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="flex-1 mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8 py-8">
        @if(session('status'))
            <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-slate-900 py-5 text-center text-xs text-slate-500">
        fail4free.de — Private Community
    </footer>
</body>
</html>
