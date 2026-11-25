<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndActiveUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_login_and_access_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'member@fail4free.de',
            'password' => 'super-secret-password',
            'is_active' => true,
            'is_admin' => false,
        ]);

        $this->post('/login', [
            'email' => 'member@fail4free.de',
            'password' => 'super-secret-password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->get('/start')->assertOk();
        $this->get('/admin/einladungen')->assertForbidden();
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'disabled@fail4free.de',
            'password' => 'super-secret-password',
            'is_active' => false,
        ]);

        $this->post('/login', [
            'email' => 'disabled@fail4free.de',
            'password' => 'super-secret-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_deactivated_logged_in_user_is_kicked_out_immediately(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/start')
            ->assertOk();

        $user->update(['is_active' => false]);

        $this->actingAs($user->fresh())
            ->get('/start')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_can_update_profile_and_password(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@fail4free.de',
            'password' => 'old-password-123',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->put('/profil', [
                'name' => 'New Name',
                'email' => 'new@fail4free.de',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@fail4free.de',
        ]);

        $this->actingAs($user->fresh())
            ->put('/profil/passwort', [
                'current_password' => 'old-password-123',
                'password' => 'new-password-456',
                'password_confirmation' => 'new-password-456',
            ])
            ->assertRedirect();
    }
}
