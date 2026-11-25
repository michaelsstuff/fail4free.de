<?php

namespace Tests\Unit;

use App\Models\Invite;
use PHPUnit\Framework\TestCase;

class InviteTokenTest extends TestCase
{
    public function test_generate_token_returns_unique_64_char_hex_strings(): void
    {
        $first = Invite::generateToken();
        $second = Invite::generateToken();

        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $first);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $second);
        $this->assertNotSame($first, $second);
    }

    public function test_hash_token_produces_deterministic_sha256_digest(): void
    {
        $plainToken = 'test-invite-token';

        $hash = Invite::hashToken($plainToken);

        $this->assertSame(hash('sha256', $plainToken), $hash);
        $this->assertSame($hash, Invite::hashToken($plainToken));
        $this->assertNotSame($plainToken, $hash);
    }
}
