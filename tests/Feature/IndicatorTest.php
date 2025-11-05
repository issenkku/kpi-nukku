<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Indicator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;

class IndicatorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // ✅ ensure roles exist for guard 'web'
        foreach (['super_admin', 'user'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    protected function makeAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        return $user;
    }

    protected function makeUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        return $user;
    }

    #[Test]
    public function admin_can_create_indicator(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->postJson('/api/indicators', [
            'name'      => 'Test Indicator',
            'code'      => 'IND-001',
            'max_score' => 100,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('indicators', ['code' => 'IND-001']);
    }

    #[Test]
    public function user_cannot_create_indicator(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->postJson('/api/indicators', [
            'name'      => 'Test User Indicator',
            'code'      => 'IND-002',
            'max_score' => 50,
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_update_indicator(): void
    {
        $admin = $this->makeAdmin();
        $indicator = Indicator::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($admin)->putJson("/api/indicators/{$indicator->id}", [
            'name'      => 'New Name',
            'code'      => $indicator->code,
            'max_score' => $indicator->max_score,
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'New Name']);
    }

    #[Test]
    public function admin_can_delete_indicator(): void
    {
        $admin = $this->makeAdmin();
        $indicator = Indicator::factory()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/indicators/{$indicator->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('indicators', ['id' => $indicator->id]);
    }

    #[Test]
    public function user_cannot_delete_indicator(): void
    {
        $user = $this->makeUser();
        $indicator = Indicator::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/indicators/{$indicator->id}");

        $response->assertStatus(403);
    }

    public function definition(): array
    {
        return [
            'name'       => $this->faker->sentence(),
            'code'       => 'IND-' . $this->faker->unique()->randomNumber(3),
            'max_score'  => $this->faker->numberBetween(50, 100),
            'score_acc'  => 0,
            'status'     => 1,
            'deadline'   => $this->faker->date(), // ✅ แก้เพิ่ม
        ];
    }
}
