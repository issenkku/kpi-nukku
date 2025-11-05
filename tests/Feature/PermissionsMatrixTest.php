<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PermissionsMatrixTest extends TestCase
{
    use RefreshDatabase;

    private function grant(User $user, string $permission): void
    {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
    }

    #[Test]
    public function indicator_create_requires_permission(): void
    {
        $user = User::factory()->create();

        // Without permission => 403
        $this->actingAs($user, 'sanctum')
            ->get('/indicator/create')
            ->assertStatus(403);

        // With permission => 200
        $this->grant($user, 'create-indicator');
        $this->actingAs($user, 'sanctum')
            ->get('/indicator/create')
            ->assertStatus(200);
    }

    #[Test]
    public function users_index_requires_view_users_permission(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')->get('/users')->assertStatus(403);

        $this->grant($user, 'view-users');
        $this->actingAs($user, 'sanctum')->get('/users')->assertStatus(200);
    }

    #[Test]
    public function dashboard_requires_view_dashboard_permission(): void
    {
        $user = User::factory()->create();
        // Only assert denial here to avoid DB driver-specific dashboard logic
        $this->actingAs($user, 'sanctum')->get('/dashboard')->assertStatus(403);
    }

    #[Test]
    public function indicator_index_requires_view_indicator_dashboard_permission(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')->get('/indicator')->assertStatus(403);
        $this->grant($user, 'view-indicator-dashboard');
        $this->actingAs($user, 'sanctum')->get('/indicator')->assertStatus(200);
    }
}
