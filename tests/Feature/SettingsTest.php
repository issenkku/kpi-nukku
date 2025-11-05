<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private function grant(User $user, string $permission): void
    {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
    }

    #[Test]
    public function index_requires_permission(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')->get('/settings')->assertStatus(403);
        $this->grant($user, 'view-settings');
        $this->actingAs($user, 'sanctum')->get('/settings')->assertStatus(200);
    }

    #[Test]
    public function can_store_and_update_settings_with_permissions(): void
    {
        $admin = User::factory()->create();
        $this->grant($admin, 'view-settings');
        $this->grant($admin, 'create-settings');
        $this->grant($admin, 'edit-settings');

        // Store (creates or updates id=1)
        $this->actingAs($admin, 'sanctum')
            ->post('/settings/store', [
                'title' => 'System Title',
                'notify_date1' => now()->toDateString(),
                'notify_date2' => now()->addDay()->toDateString(),
                'message' => 'Hello',
            ])->assertStatus(302);

        $this->assertDatabaseHas('settings', ['id' => 1, 'title' => 'System Title']);

        // Update
        $this->actingAs($admin, 'sanctum')
            ->put('/settings/1', [
                'title' => 'New Title',
                'notify_date1' => now()->toDateString(),
            ])->assertStatus(302);

        $this->assertDatabaseHas('settings', ['id' => 1, 'title' => 'New Title']);
    }
}

