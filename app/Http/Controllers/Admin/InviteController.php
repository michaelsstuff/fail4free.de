<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InviteMail;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class InviteController extends Controller
{
    public function index(): View
    {
        $invites = Invite::query()
            ->with('inviter')
            ->latest()
            ->get();

        return view('admin.invites.index', [
            'invites' => $invites,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $email = Str::lower(trim($validated['email']));

        if (User::query()->where('email', $email)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Für diese E-Mail-Adresse existiert bereits ein Konto.']);
        }

        if (Invite::query()->pending()->where('email', $email)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Für diese E-Mail-Adresse gibt es bereits eine offene Einladung.']);
        }

        $plainToken = Invite::generateToken();

        $invite = Invite::query()->create([
            'email' => $email,
            'token_hash' => Invite::hashToken($plainToken),
            'invited_by' => $request->user()->id,
            'expires_at' => now()->addDays(Invite::DEFAULT_EXPIRY_DAYS),
        ]);

        try {
            Mail::to($invite->email)->send(new InviteMail($invite, $plainToken));
        } catch (Throwable $e) {
            $invite->delete();
            Log::error('Invite mail failed to send', ['email' => $email, 'error' => $e->getMessage()]);

            return back()
                ->withInput()
                ->withErrors(['email' => 'Einladungs-E-Mail konnte nicht gesendet werden. Bitte SMTP-Einstellungen prüfen.']);
        }

        return redirect()
            ->route('admin.invites.index')
            ->with('status', "Einladung an {$email} wurde versendet.");
    }

    public function resend(Invite $invite): RedirectResponse
    {
        if ($invite->isAccepted()) {
            return back()->withErrors(['invite' => 'Angenommene Einladungen können nicht erneut gesendet werden.']);
        }

        if (User::query()->where('email', $invite->email)->exists()) {
            return back()->withErrors(['invite' => 'Für diese E-Mail-Adresse existiert bereits ein Konto.']);
        }

        $previousHash = $invite->token_hash;
        $previousExpiry = $invite->expires_at;

        $plainToken = Invite::generateToken();

        $invite->update([
            'token_hash' => Invite::hashToken($plainToken),
            'expires_at' => now()->addDays(Invite::DEFAULT_EXPIRY_DAYS),
        ]);

        try {
            Mail::to($invite->email)->send(new InviteMail($invite, $plainToken));
        } catch (Throwable $e) {
            $invite->update([
                'token_hash' => $previousHash,
                'expires_at' => $previousExpiry,
            ]);
            Log::error('Invite resend failed', ['invite_id' => $invite->id, 'error' => $e->getMessage()]);

            return back()->withErrors(['invite' => 'E-Mail konnte nicht erneut gesendet werden. Bitte SMTP-Einstellungen prüfen.']);
        }

        return back()->with('status', "Neue Einladung an {$invite->email} wurde versendet (alter Link ist ungültig).");
    }

    public function destroy(Invite $invite): RedirectResponse
    {
        if ($invite->isAccepted()) {
            return back()->withErrors(['invite' => 'Angenommene Einladungen können nicht widerrufen werden.']);
        }

        $email = $invite->email;
        $invite->delete();

        return back()->with('status', "Einladung für {$email} wurde widerrufen.");
    }
}
