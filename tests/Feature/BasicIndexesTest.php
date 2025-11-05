<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BasicIndexesTest extends TestCase
{
    use RefreshDatabase;

    private function grant(User $user, string $permission): void
    {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
    }

    #[Test]
    public function departments_index_requires_permission(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')->get('/departments')->assertStatus(403);
        $this->grant($user, 'view-departments');
        $this->actingAs($user, 'sanctum')->get('/departments')->assertStatus(200);
    }

    #[Test]
    public function categories_index_requires_permission(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')->get('/categories')->assertStatus(403);
        $this->grant($user, 'view-categories');
        $this->actingAs($user, 'sanctum')->get('/categories')->assertStatus(200);
    }

    #[Test]
    public function standards_index_requires_permission(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')->get('/standards')->assertStatus(403);
        $this->grant($user, 'view-standards');
        $this->actingAs($user, 'sanctum')->get('/standards')->assertStatus(200);
    }
}

