<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DepartmentsCrudTest extends TestCase
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

        // Index 403 then 200 when granted
        $this->actingAs($admin, 'sanctum')->get('/departments')->assertStatus(403);
        $this->grant($admin, 'view-departments');
        $this->actingAs($admin, 'sanctum')->get('/departments')->assertOk();

        // Create requires create-departments
        $this->actingAs($admin, 'sanctum')
            ->post('/departments/store', ['name' => 'QA'])
            ->assertStatus(403);

        $this->grant($admin, 'create-departments');
        $this->actingAs($admin, 'sanctum')
            ->post('/departments/store', ['name' => 'QA'])
            ->assertStatus(302);
        $dept = Department::where('name', 'QA')->firstOrFail();

        // Update requires edit-departments
        $this->actingAs($admin, 'sanctum')
            ->put("/departments/{$dept->id}", ['name' => 'Quality Assurance'])
            ->assertStatus(403);

        $this->grant($admin, 'edit-departments');
        $this->actingAs($admin, 'sanctum')
            ->put("/departments/{$dept->id}", ['name' => 'Quality Assurance'])
            ->assertStatus(302);
        $dept->refresh();
        $this->assertSame('Quality Assurance', $dept->name);

        // Delete requires delete-departments
        $this->actingAs($admin, 'sanctum')
            ->delete("/departments/{$dept->id}")
            ->assertStatus(403);

        $this->grant($admin, 'delete-departments');
        $this->actingAs($admin, 'sanctum')
            ->delete("/departments/{$dept->id}")
            ->assertStatus(302);
        $this->assertDatabaseMissing('departments', ['id' => $dept->id]);
    }
}

