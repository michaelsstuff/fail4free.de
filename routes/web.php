<?php

use App\Http\Controllers\Admin\GameServerController;
use App\Http\Controllers\Admin\InviteController;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeployController;
use App\Http\Controllers\InviteRegistrationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Öffentliche Landingpage
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
})->name('home');

// Auth (nur für Gäste)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/passwort-vergessen', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/passwort-vergessen', [PasswordResetController::class, 'sendResetLink'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get('/passwort-zuruecksetzen/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/passwort-zuruecksetzen', [PasswordResetController::class, 'reset'])
        ->middleware('throttle:5,1')
        ->name('password.update');

    Route::get('/einladung/{token}', [InviteRegistrationController::class, 'show'])->name('invites.accept.show');
    Route::post('/einladung/{token}', [InviteRegistrationController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('invites.accept.store');
});

// Eingeloggt + aktiv
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/start', DashboardController::class)->name('dashboard');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/passwort', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Admin-Bereich
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        // Einladungen
        Route::get('/einladungen', [InviteController::class, 'index'])->name('invites.index');
        Route::post('/einladungen', [InviteController::class, 'store'])->name('invites.store');
        Route::post('/einladungen/{invite}/erneut-senden', [InviteController::class, 'resend'])->name('invites.resend');
        Route::delete('/einladungen/{invite}', [InviteController::class, 'destroy'])->name('invites.destroy');

        // Dedicated Server
        Route::get('/server', [GameServerController::class, 'index'])->name('servers.index');
        Route::get('/server/neu', [GameServerController::class, 'create'])->name('servers.create');
        Route::post('/server', [GameServerController::class, 'store'])->name('servers.store');
        Route::get('/server/{server}/bearbeiten', [GameServerController::class, 'edit'])->name('servers.edit');
        Route::put('/server/{server}', [GameServerController::class, 'update'])->name('servers.update');
        Route::delete('/server/{server}', [GameServerController::class, 'destroy'])->name('servers.destroy');

        // Verbindungen (Links)
        Route::get('/verbindungen', [LinkController::class, 'index'])->name('links.index');
        Route::get('/verbindungen/neu', [LinkController::class, 'create'])->name('links.create');
        Route::post('/verbindungen', [LinkController::class, 'store'])->name('links.store');
        Route::get('/verbindungen/{link}/bearbeiten', [LinkController::class, 'edit'])->name('links.edit');
        Route::put('/verbindungen/{link}', [LinkController::class, 'update'])->name('links.update');
        Route::delete('/verbindungen/{link}', [LinkController::class, 'destroy'])->name('links.destroy');

        // Mitglieder
        Route::get('/mitglieder', [MemberController::class, 'index'])->name('members.index');
        Route::patch('/mitglieder/{user}/admin', [MemberController::class, 'toggleAdmin'])->name('members.toggle-admin');
        Route::patch('/mitglieder/{user}/aktiv', [MemberController::class, 'toggleActive'])->name('members.toggle-active');
    });
});

// Geschützter Web-Deploy-Endpoint (nur aktiv wenn DEPLOY_KEY in .env gesetzt)
Route::get('/__deploy', [DeployController::class, 'show'])->name('deploy.show');
Route::post('/__deploy', [DeployController::class, 'run'])
    ->middleware('throttle:5,1')
    ->name('deploy.run');
