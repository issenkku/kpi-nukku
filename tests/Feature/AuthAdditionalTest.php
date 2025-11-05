<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthAdditionalTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function invalid_login_returns_401_json(): void
    {
        $response = $this->postJson('/login', [
            'email' => 'noone@example.test',
            'password' => 'wrong',
        ]);

        $response->assertStatus(401)
                 ->assertJsonStructure(['message']);
    }
}

