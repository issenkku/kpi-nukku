<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Indicator;
use App\Models\Standard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class IndicatorControllerE2eTest extends TestCase
{
    use RefreshDatabase;

    private function grant(User $user, string $permission): void
    {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
    }

    private function seedRefs(): array
    {
        $standard = Standard::create(['name' => 'Std E2E']);
        $category = Category::create(['name' => 'Cat E2E', 'standard_id' => $standard->id, 'max_score' => 100]);
        $collector = User::factory()->create();
        return compact('standard', 'category', 'collector');
    }

    #[Test]
    public function store_creates_criteria_and_checklist_from_multiCounts(): void
    {
        $admin = User::factory()->create();
        $this->grant($admin, 'create-indicator');
        $refs = $this->seedRefs();

        $code = 'E2E-' . Str::random(6);

        $payload = [
            'year' => 2025,
            'name' => 'E2E Indicator',
            'code' => $code,
            'max_score' => 100,
            'standard_id' => $refs['standard']->id,
            'category_id' => $refs['category']->id,
            'deadline' => now()->toDateString(),
            'user_ids' => [$refs['collector']->id],
            'criteria' => [
                ['sequence' => 1, 'name' => 'C1'],
                ['sequence' => 2, 'name' => 'C2'],
                ['sequence' => 3, 'name' => 'C3'],
            ],
            'multiCounts' => [
                ['count' => 2, 'score' => 5],
            ],
        ];

        $this->actingAs($admin, 'sanctum')
            ->post('/indicator/store', $payload)
            ->assertStatus(302);

        $indicator = Indicator::where('code', $code)->firstOrFail();
        $this->assertEquals(3, $indicator->criterias()->count());
        // 3 choose 2 = 3 combinations
        $this->assertEquals(3, $indicator->checklistItems()->count());
    }

    #[Test]
    public function update_syncs_criteria_and_regenerates_checklist(): void
    {
        $admin = User::factory()->create();
        $this->grant($admin, 'create-indicator');
        $this->grant($admin, 'edit-indicator');
        $refs = $this->seedRefs();

        // Create initial
        $code = 'E2E-' . Str::random(6);
        $create = [
            'year' => 2025,
            'name' => 'E2E Indicator',
            'code' => $code,
            'max_score' => 100,
            'standard_id' => $refs['standard']->id,
            'category_id' => $refs['category']->id,
            'deadline' => now()->toDateString(),
            'user_ids' => [$refs['collector']->id],
            'criteria' => [
                ['sequence' => 1, 'name' => 'C1'],
                ['sequence' => 2, 'name' => 'C2'],
                ['sequence' => 3, 'name' => 'C3'],
            ],
            'multiCounts' => [
                ['count' => 2, 'score' => 5],
            ],
        ];

        $this->actingAs($admin, 'sanctum')->post('/indicator/store', $create)->assertStatus(302);
        $indicator = Indicator::where('code', $code)->firstOrFail();

        $existing = $indicator->criterias()->orderBy('sequence')->get();

        // Update: keep first, remove others, add a new one; regenerate combos for 2 criteria => 1 combo
        $update = [
            'year' => 2025,
            'name' => 'E2E Indicator Updated',
            'code' => $code,
            'max_score' => 100,
            'standard_id' => $refs['standard']->id,
            'category_id' => $refs['category']->id,
            'deadline' => now()->toDateString(),
            'user_ids' => [$refs['collector']->id],
            'criteria' => [
                ['id' => $existing[0]->id, 'sequence' => 1, 'name' => 'C1 Updated'],
                ['sequence' => 2, 'name' => 'C4'],
            ],
            'multiCounts' => [
                ['count' => 2, 'score' => 10],
            ],
        ];

        $this->actingAs($admin, 'sanctum')
            ->put("/indicator/{$indicator->id}", $update)
            ->assertStatus(302);

        $indicator->refresh();
        $this->assertEquals(2, $indicator->criterias()->count());
        $this->assertEquals(1, $indicator->checklistItems()->count());
    }
}

