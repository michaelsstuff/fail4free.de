<?php

namespace App\Http\Controllers;

use App\Models\Invite;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class InviteRegistrationController extends Controller
{
    public function show(string $token): View|Response
    {
        $invite = Invite::findByPlainToken($token);

        if ($invite === null) {
            return response()->view('auth.invite-invalid', [
                'reason' => 'Dieser Einladungslink ist ungültig oder wurde widerrufen.',
            ], 404);
        }

        if ($invite->isAccepted()) {
            return response()->view('auth.invite-invalid', [
                'reason' => 'Diese Einladung wurde bereits verwendet.',
            ], 410);
        }

        if ($invite->isExpired()) {
            return response()->view('auth.invite-invalid', [
                'reason' => 'Diese Einladung ist abgelaufen. Bitte bitte einen Admin um eine neue Einladung.',
            ], 410);
        }

        if (User::query()->where('email', $invite->email)->exists()) {
            return response()->view('auth.invite-invalid', [
                'reason' => 'Für diese E-Mail-Adresse existiert bereits ein Konto.',
            ], 409);
        }

        return view('auth.register-invite', [
            'invite' => $invite,
            'token' => $token,
        ]);
    }

    public function store(Request $request, string $token): RedirectResponse|Response
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', PasswordRule::min(10)],
        ]);

        $user = DB::transaction(function () use ($token, $validated): ?User {
            $invite = Invite::query()
                ->where('token_hash', Invite::hashToken($token))
                ->lockForUpdate()
                ->first();

            if ($invite === null || ! $invite->isUsable()) {
                return null;
            }

            if (User::query()->where('email', $invite->email)->exists()) {
                return null;
            }

            $user = User::query()->create([
                'name' => trim($validated['name']),
                'email' => $invite->email,
                'password' => $validated['password'],
                'email_verified_at' => now(),
                'is_admin' => false,
                'is_active' => true,
                'invited_by' => $invite->invited_by,
            ]);

            $invite->update([
                'accepted_at' => now(),
            ]);

            return $user;
        });

        if ($user === null) {
            return response()->view('auth.invite-invalid', [
                'reason' => 'Diese Einladung ist ungültig, abgelaufen oder wurde bereits verwendet.',
            ], 410);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('status', 'Willkommen bei fail4free.de!');
    }
}
