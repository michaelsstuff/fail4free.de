<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_returns_ok_and_blocks_dashboard_for_guests(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Zugang nur per Einladung');

        $this->get('/start')
            ->assertRedirect(route('login'));
    }
}
