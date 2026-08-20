<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_login_and_logout()
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        // login
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);
        $response->assertOk()->assertJson(['redirect' => '/dashboard']);
        $this->assertAuthenticatedAs($user);

        // logout
        $response = $this->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
