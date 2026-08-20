<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UsersCrudTest extends TestCase
{
    use RefreshDatabase;

    private function grant(User $user, string $permission): void
    {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
    }

    private function ensureRole(string $name = 'user'): Role
    {
        return Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
    }

    #[Test]
    public function can_create_update_delete_user_with_permissions(): void
    {
        $admin = User::factory()->create();
        $this->grant($admin, 'view-users');
        $this->grant($admin, 'create-users');
        $this->grant($admin, 'edit-users');
        $this->grant($admin, 'delete-users');
        $role = $this->ensureRole('user');
        $dept = Department::factory()->create();

        // Create
        $email = 'new.user@example.test';
        $this->actingAs($admin, 'sanctum')
            ->post('/users', [
                'first_name' => 'New',
                'last_name' => 'User',
                'password' => 'password123!',
                'password_confirmation' => 'password123!',
                'email' => $email,
                'phone' => '0123456789',
                'department_id' => $dept->id,
                'status' => 1,
                'role' => $role->name,
            ])->assertStatus(302);

        $created = User::where('email', $email)->firstOrFail();
        $this->assertDatabaseHas('users', ['email' => $email]);

        // Update (change name and phone)
        $this->actingAs($admin, 'sanctum')
            ->put("/users/{$created->id}", [
                'first_name' => 'Updated',
                'last_name' => 'Name',
                'email' => $email,
                'phone' => '0987654321',
                'department_id' => $dept->id,
                'status' => 1,
                'role' => $role->name,
            ])->assertStatus(302);

        $created->refresh();
        $this->assertSame('Updated', $created->first_name);
        $this->assertSame('Name', $created->last_name);
        $this->assertSame('0987654321', $created->phone);

        // Delete
        $this->actingAs($admin, 'sanctum')
            ->delete("/users/{$created->id}")
            ->assertStatus(302);

        $this->assertDatabaseMissing('users', ['id' => $created->id]);
    }
}
