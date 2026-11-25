<?php

namespace App\Http\Controllers;

use App\Models\GameServer;
use App\Models\Link;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $links = Link::query()
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();

        $serversByGame = GameServer::query()
            ->where('is_active', true)
            ->orderBy('game')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('game');

        return view('dashboard', [
            'links' => $links,
            'serversByGame' => $serversByGame,
        ]);
    }
}
