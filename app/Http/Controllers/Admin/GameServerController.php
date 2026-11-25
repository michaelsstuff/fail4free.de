<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameServer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GameServerController extends Controller
{
    public function index(): View
    {
        $servers = GameServer::query()
            ->orderBy('game')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.servers.index', [
            'servers' => $servers,
        ]);
    }

    public function create(): View
    {
        return view('admin.servers.form', [
            'server' => new GameServer(['is_active' => true, 'sort_order' => 0]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateServer($request);

        $data = [
            'name' => $validated['name'],
            'game' => $validated['game'],
            'host' => $validated['host'],
            'port' => $validated['port'] ?? null,
            'password' => ($validated['password'] ?? '') !== '' ? $validated['password'] : null,
            'description' => $validated['description'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ];

        GameServer::query()->create($data);

        return redirect()
            ->route('admin.servers.index')
            ->with('status', 'Server wurde angelegt.');
    }

    public function edit(GameServer $server): View
    {
        return view('admin.servers.form', [
            'server' => $server,
        ]);
    }

    public function update(Request $request, GameServer $server): RedirectResponse
    {
        $validated = $this->validateServer($request);

        $data = [
            'name' => $validated['name'],
            'game' => $validated['game'],
            'host' => $validated['host'],
            'port' => $validated['port'] ?? null,
            'description' => $validated['description'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ];

        if ($request->boolean('clear_password')) {
            $data['password'] = null;
        } elseif (($validated['password'] ?? '') !== '') {
            $data['password'] = $validated['password'];
        }

        $server->update($data);

        return redirect()
            ->route('admin.servers.index')
            ->with('status', 'Server wurde aktualisiert.');
    }

    public function destroy(GameServer $server): RedirectResponse
    {
        $server->delete();

        return redirect()
            ->route('admin.servers.index')
            ->with('status', 'Server wurde gelöscht.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateServer(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'game' => ['required', 'string', 'max:255'],
            'host' => ['required', 'string', 'max:255'],
            'port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'password' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:4000'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'clear_password' => ['nullable', 'boolean'],
        ]);
    }
}
