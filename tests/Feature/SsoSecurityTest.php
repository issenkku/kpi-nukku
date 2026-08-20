<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SsoSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'sso.client_id' => 'client-id',
            'sso.client_secret' => 'client-secret',
            'sso.redirect_url' => 'https://app.example.test/auth',
            'sso.api_base_url' => 'https://sso-api.example.test',
        ]);
    }

    #[Test]
    public function callback_rejects_an_invalid_oauth_state(): void
    {
        Http::fake();

        $this->get('/auth?state=unexpected&code=code')
            ->assertRedirect('/login')
            ->assertSessionHas('error');

        Http::assertNothingSent();
    }

    #[Test]
    public function inactive_local_account_cannot_bypass_suspension_with_sso(): void
    {
        User::factory()->inactive()->create(['email' => 'inactive@example.test']);
        Http::fake([
            'https://sso-api.example.test/auth.token' => Http::response([
                'ok' => true,
                'accessToken' => 'access-token',
            ]),
            'https://sso-api.example.test/user.profile' => Http::response([
                'profile' => ['email' => 'inactive@example.test'],
            ]),
        ]);

        $this->withSession(['sso_state' => 'expected-state'])
            ->get('/auth?state=expected-state&code=code')
            ->assertRedirect('/login')
            ->assertSessionHas('error');

        $this->assertGuest();
    }
}
