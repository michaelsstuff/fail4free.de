<?php

namespace Tests\Feature;

use App\Models\GameServer;
use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminContentAndMembersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_links_and_servers_with_encrypted_password(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $this->actingAs($admin)
            ->post('/admin/verbindungen', [
                'type' => 'teamspeak',
                'label' => 'Haupt-TS',
                'value' => 'ts.fail4free.de',
                'note' => 'Kein Passwort',
                'sort_order' => 1,
            ])
            ->assertRedirect(route('admin.links.index'));

        $this->actingAs($admin)
            ->post('/admin/server', [
                'game' => 'Valheim',
                'name' => 'Midgard',
                'host' => 'games.fail4free.de',
                'port' => 2456,
                'password' => 'geheimes-server-pw',
                'description' => 'Co-op Welt',
                'notes' => 'Keine Mods',
                'is_active' => 1,
                'sort_order' => 1,
            ])
            ->assertRedirect(route('admin.servers.index'));

        $server = GameServer::query()->firstOrFail();
        $rawPassword = DB::table('game_servers')->where('id', $server->id)->value('password');

        $this->assertNotSame('geheimes-server-pw', $rawPassword);
        $this->assertSame('geheimes-server-pw', $server->password);

        // Editing server with blank password keeps existing encrypted password
        $this->actingAs($admin)
            ->put("/admin/server/{$server->id}", [
                'game' => 'Valheim',
                'name' => 'Midgard Updated',
                'host' => 'games.fail4free.de',
                'port' => 2456,
                'password' => '',
                'is_active' => 1,
                'sort_order' => 1,
            ])
            ->assertRedirect(route('admin.servers.index'));

        $this->assertSame('geheimes-server-pw', $server->fresh()->password);

        $this->actingAs($admin)
            ->get('/start')
            ->assertOk()
            ->assertSee('Haupt-TS')
            ->assertSee('ts.fail4free.de')
            ->assertSee('Midgard Updated')
            ->assertSee('games.fail4free.de:2456');

        $this->assertSame(1, Link::query()->count());
    }

    public function test_last_active_admin_cannot_demote_or_deactivate_self(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $this->actingAs($admin)
            ->patch("/admin/mitglieder/{$admin->id}/admin")
            ->assertSessionHasErrors('member');

        $this->assertTrue($admin->fresh()->is_admin);

        $this->actingAs($admin)
            ->patch("/admin/mitglieder/{$admin->id}/aktiv")
            ->assertSessionHasErrors('member');

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_admin_can_promote_and_deactivate_other_member(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $member = User::factory()->create(['is_admin' => false, 'is_active' => true]);

        $this->actingAs($admin)
            ->patch("/admin/mitglieder/{$member->id}/admin")
            ->assertRedirect();

        $this->assertTrue($member->fresh()->is_admin);

        $this->actingAs($admin)
            ->patch("/admin/mitglieder/{$member->id}/aktiv")
            ->assertRedirect();

        $this->assertFalse($member->fresh()->is_active);
    }
}
