<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(): View
    {
        $members = User::query()
            ->with('inviter')
            ->orderByDesc('is_admin')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('admin.members.index', [
            'members' => $members,
        ]);
    }

    public function toggleAdmin(Request $request, User $user): RedirectResponse
    {
        if ($user->is_admin) {
            $otherActiveAdminExists = User::query()
                ->where('is_admin', true)
                ->where('is_active', true)
                ->whereKeyNot($user->id)
                ->exists();

            if (! $otherActiveAdminExists) {
                return back()->withErrors([
                    'member' => 'Dem letzten aktiven Admin können die Admin-Rechte nicht entzogen werden.',
                ]);
            }
        }

        $user->update(['is_admin' => ! $user->is_admin]);

        $message = $user->is_admin
            ? "{$user->name} hat jetzt Admin-Rechte."
            : "Admin-Rechte von {$user->name} wurden entfernt.";

        return back()->with('status', $message);
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        if ($user->is_active) {
            if ($user->is($request->user())) {
                return back()->withErrors([
                    'member' => 'Du kannst dein eigenes Konto nicht deaktivieren.',
                ]);
            }

            if ($user->is_admin) {
                $otherActiveAdminExists = User::query()
                    ->where('is_admin', true)
                    ->where('is_active', true)
                    ->whereKeyNot($user->id)
                    ->exists();

                if (! $otherActiveAdminExists) {
                    return back()->withErrors([
                        'member' => 'Der letzte aktive Admin kann nicht deaktiviert werden.',
                    ]);
                }
            }
        }

        $user->update(['is_active' => ! $user->is_active]);

        $message = $user->is_active
            ? "{$user->name} wurde wieder aktiviert."
            : "{$user->name} wurde deaktiviert.";

        return back()->with('status', $message);
    }
}
