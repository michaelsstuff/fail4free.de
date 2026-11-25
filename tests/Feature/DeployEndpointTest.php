<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeployEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_deploy_returns_404_when_key_is_not_configured(): void
    {
        config()->set('deploy.key', '');

        $this->get('/__deploy')->assertNotFound();
        $this->post('/__deploy', ['deploy_key' => 'anything'])->assertNotFound();
    }

    public function test_deploy_rejects_wrong_key_with_403(): void
    {
        config()->set('deploy.key', 'expected-secret-key');

        $this->get('/__deploy')->assertOk();

        $this->post('/__deploy', ['deploy_key' => 'wrong-key'])
            ->assertForbidden();
    }

    public function test_deploy_runs_with_valid_key_and_seeds_initial_admin_idempotently(): void
    {
        config()->set('deploy.key', 'expected-secret-key');
        config()->set('deploy.admin.name', 'Boss');
        config()->set('deploy.admin.email', 'boss@fail4free.de');
        config()->set('deploy.admin.password', 'initial-secret-pw');

        $this->postJson('/__deploy', [], ['X-Deploy-Key' => 'expected-secret-key'])
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'seeded_admin' => true,
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'boss@fail4free.de',
            'is_admin' => true,
            'is_active' => true,
        ]);

        // Second call is idempotent and does not seed again
        $this->postJson('/__deploy', [], ['X-Deploy-Key' => 'expected-secret-key'])
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'seeded_admin' => false,
            ]);

        $this->assertSame(1, User::query()->count());
    }
}
