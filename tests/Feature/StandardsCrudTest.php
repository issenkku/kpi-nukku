<?php

namespace Tests\Feature;

use App\Models\Standard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class StandardsCrudTest extends TestCase
{
    use RefreshDatabase;

    private function grant(User $user, string $permission): void
    {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
    }

    #[Test]
    public function full_crud_with_permissions(): void
    {
        $admin = User::factory()->create();

        // Index 403 then 200
        $this->actingAs($admin, 'sanctum')->get('/standards')->assertStatus(403);
        $this->grant($admin, 'view-standards');
        $this->actingAs($admin, 'sanctum')->get('/standards')->assertOk();

        // Create requires create-standards
        $this->actingAs($admin, 'sanctum')->post('/standards/store', ['name' => 'STD-1'])->assertStatus(403);
        $this->grant($admin, 'create-standards');
        $this->actingAs($admin, 'sanctum')->post('/standards/store', ['name' => 'STD-1'])->assertStatus(302);
        $std = Standard::where('name', 'STD-1')->firstOrFail();

        // Update requires edit-standards
        $this->actingAs($admin, 'sanctum')->put("/standards/{$std->id}", ['name' => 'STD-ONE'])->assertStatus(403);
        $this->grant($admin, 'edit-standards');
        $this->actingAs($admin, 'sanctum')->put("/standards/{$std->id}", ['name' => 'STD-ONE'])->assertStatus(302);
        $std->refresh();
        $this->assertSame('STD-ONE', $std->name);

        // Delete requires delete-standards (only if no indicators)
        $this->actingAs($admin, 'sanctum')->delete("/standards/{$std->id}")->assertStatus(403);
        $this->grant($admin, 'delete-standards');
        $this->actingAs($admin, 'sanctum')->delete("/standards/{$std->id}")->assertStatus(302);
        $this->assertDatabaseMissing('standards', ['id' => $std->id]);
    }
}

