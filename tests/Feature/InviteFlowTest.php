<?php

namespace Tests\Feature;

use App\Mail\InviteMail;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InviteFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_send_invite_and_recipient_can_register_once(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post('/admin/einladungen', ['email' => 'Freund@Example.de'])
            ->assertRedirect(route('admin.invites.index'));

        $invite = Invite::query()->where('email', 'freund@example.de')->firstOrFail();

        $acceptUrl = null;
        Mail::assertSent(InviteMail::class, function (InviteMail $mail) use (&$acceptUrl) {
            $acceptUrl = $mail->acceptUrl;

            return $mail->hasTo('freund@example.de');
        });

        $this->assertNotNull($acceptUrl);
        $plainToken = basename(parse_url($acceptUrl, PHP_URL_PATH));
        $this->assertNotSame($plainToken, $invite->token_hash);

        // Logout admin so guest can register
        $this->post('/logout');

        $this->get("/einladung/{$plainToken}")
            ->assertOk()
            ->assertSee('freund@example.de');

        $this->post("/einladung/{$plainToken}", [
            'name' => 'Max',
            'password' => 'sicheres-passwort-123',
            'password_confirmation' => 'sicheres-passwort-123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'freund@example.de',
            'name' => 'Max',
            'is_admin' => false,
            'is_active' => true,
            'invited_by' => $admin->id,
        ]);

        // Reusing the same invite link fails
        $this->post('/logout');
        $this->get("/einladung/{$plainToken}")->assertStatus(410);
    }

    public function test_resending_invite_invalidates_old_token_and_creates_new_one(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $oldToken = Invite::generateToken();
        $invite = Invite::query()->create([
            'email' => 'resend@example.de',
            'token_hash' => Invite::hashToken($oldToken),
            'invited_by' => $admin->id,
            'expires_at' => now()->subDay(),
        ]);

        $this->actingAs($admin)
            ->post("/admin/einladungen/{$invite->id}/erneut-senden")
            ->assertRedirect();

        $newUrl = null;
        Mail::assertSent(InviteMail::class, function (InviteMail $mail) use (&$newUrl) {
            $newUrl = $mail->acceptUrl;

            return $mail->hasTo('resend@example.de');
        });

        $newToken = basename(parse_url((string) $newUrl, PHP_URL_PATH));
        $this->assertNotSame($oldToken, $newToken);

        $this->post('/logout');

        $this->get("/einladung/{$oldToken}")->assertNotFound();
        $this->get("/einladung/{$newToken}")->assertOk();
    }

    public function test_expired_invite_is_rejected_and_pruned(): void
    {
        $token = Invite::generateToken();
        Invite::query()->create([
            'email' => 'expired@example.de',
            'token_hash' => Invite::hashToken($token),
            'expires_at' => now()->subMinute(),
        ]);

        $this->get("/einladung/{$token}")->assertStatus(410);

        $this->artisan('invites:prune')->assertSuccessful();
        $this->assertDatabaseCount('invites', 0);
    }
}
