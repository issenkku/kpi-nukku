<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
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
    public function guest_cannot_access_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    #[Test]
    public function authorized_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();
        $permission = Permission::firstOrCreate([
            'name' => 'view-dashboard',
            'guard_name' => 'web',
        ]);
        $user->givePermissionTo($permission);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk();
    }
}
