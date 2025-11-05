<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class IndicatorApiPermissionsAndValidationTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        return $user;
    }

    #[Test]
    public function validation_missing_name_returns_422(): void
    {
        $admin = $this->makeAdmin();
        $resp = $this->actingAs($admin)->postJson('/api/indicators', [
            // 'name' => 'missing',
            'code' => 'VAL-001',
            'max_score' => 10,
        ]);
        $resp->assertStatus(422)->assertJsonValidationErrors(['name']);
    }

    #[Test]
    public function delete_nonexistent_indicator_returns_404(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin)
            ->deleteJson('/api/indicators/999999')
            ->assertStatus(404);
    }

    #[Test]
    public function guest_cannot_call_api_indicators(): void
    {
        $resp = $this->postJson('/api/indicators', []);
        $this->assertTrue(in_array($resp->getStatusCode(), [401, 302], true));
    }
}
