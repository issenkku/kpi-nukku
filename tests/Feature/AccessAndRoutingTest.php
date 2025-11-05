<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccessAndRoutingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_root_redirects_to_login(): void
    {
        $this->get('/')
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_access_home(): void
    {
        $this->get('/home')->assertStatus(302);
    }

    #[Test]
    public function logged_in_can_access_home(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get('/home')
            ->assertOk();
    }
}

