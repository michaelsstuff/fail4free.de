<?php

use App\Models\Invite;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('invites:prune', function () {
    $deleted = Invite::query()
        ->whereNull('accepted_at')
        ->where('expires_at', '<=', now())
        ->delete();

    $this->info("Abgelaufene Einladungen gelöscht: {$deleted}");
})->purpose('Löscht abgelaufene, nicht angenommene Einladungen');

Schedule::command('invites:prune')->hourly();
