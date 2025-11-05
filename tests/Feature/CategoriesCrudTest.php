<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Standard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CategoriesCrudTest extends TestCase
{
    use RefreshDatabase;

    private function grant(User $user, string $permission): void
    {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
    }

    private function seedStandard(): Standard
    {
        return Standard::create(['name' => 'STD-CRUD']);
    }

    #[Test]
    public function full_crud_with_permissions(): void
    {
        $admin = User::factory()->create();
        $standard = $this->seedStandard();

        // Index 403 then 200
        $this->actingAs($admin, 'sanctum')->get('/categories')->assertStatus(403);
        $this->grant($admin, 'view-categories');
        $this->actingAs($admin, 'sanctum')->get('/categories')->assertOk();

        // Create requires create-categories
        $payload = ['name' => 'CAT-1', 'max_score' => 10, 'standard_id' => $standard->id];
        $this->actingAs($admin, 'sanctum')->post('/categories/store', $payload)->assertStatus(403);
        $this->grant($admin, 'create-categories');
        $this->actingAs($admin, 'sanctum')->post('/categories/store', $payload)->assertStatus(302);
        $cat = Category::where('name', 'CAT-1')->firstOrFail();

        // Update requires edit-categories
        $update = ['name' => 'CAT-ONE', 'max_score' => 20, 'standard_id' => $standard->id];
        $this->actingAs($admin, 'sanctum')->put("/categories/{$cat->id}", $update)->assertStatus(403);
        $this->grant($admin, 'edit-categories');
        $this->actingAs($admin, 'sanctum')->put("/categories/{$cat->id}", $update)->assertStatus(302);
        $cat->refresh();
        $this->assertSame('CAT-ONE', $cat->name);
        $this->assertEquals(20, (int) $cat->max_score);

        // Delete requires delete-categories (only if no indicators)
        $this->actingAs($admin, 'sanctum')->delete("/categories/{$cat->id}")->assertStatus(403);
        $this->grant($admin, 'delete-categories');
        $this->actingAs($admin, 'sanctum')->delete("/categories/{$cat->id}")->assertStatus(302);
        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
    }
}

