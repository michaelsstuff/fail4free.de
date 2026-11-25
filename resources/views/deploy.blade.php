@extends('layouts.guest')

@section('title', 'Deploy')

@section('content')
    <h1 class="text-xl font-semibold text-white mb-2">Deploy ausführen</h1>
    <p class="text-sm text-slate-400 mb-6">
        Führt <code class="text-slate-300">optimize:clear</code>, <code class="text-slate-300">migrate --force</code>
        und (falls noch kein User existiert) den initialen Admin-Seeder aus.
    </p>

    @if(! empty($executed))
        <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-sm text-emerald-300 space-y-2">
            <div class="font-semibold">Deploy erfolgreich abgeschlossen.</div>
            @if(! empty($seededAdmin))
                <div class="text-amber-300 text-xs">
                    Initialer Admin wurde angelegt. Bitte nach dem ersten Login <code>ADMIN_PASSWORD</code> aus <code>~/laravel/.env</code> entfernen!
                </div>
            @endif
            @foreach($outputs as $step => $out)
                <div class="pt-2">
                    <div class="text-xs font-mono uppercase text-emerald-400">{{ $step }}</div>
                    <pre class="mt-1 overflow-x-auto rounded-lg bg-slate-950/80 p-2.5 text-xs text-slate-200 whitespace-pre-wrap">{{ $out ?: 'OK' }}</pre>
                </div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('deploy.run') }}" class="space-y-4">
        @csrf

        <div>
            <label for="deploy_key" class="block text-sm font-medium text-slate-300 mb-1.5">Deploy-Key</label>
            <input id="deploy_key" name="deploy_key" type="password" required autocomplete="off"
                   class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3.5 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
        </div>

        <button type="submit"
                class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500 cursor-pointer">
            Migration &amp; Cache-Reset starten
        </button>
    </form>
@endsection
