<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Category;
use App\Models\Criteria;
use App\Models\Evidence;
use App\Models\Indicator;
use App\Models\Standard;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RolesMatrixByRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function seedIndicatorAndEvidence(): array
    {
        $standard = Standard::create(['name' => 'STD']);
        $category = Category::create(['name' => 'CAT', 'standard_id' => $standard->id, 'max_score' => 10]);
        $indicator = Indicator::create([
            'name' => 'IND',
            'code' => 'R-001',
            'max_score' => 10,
            'score_acc' => 0,
            'status' => 1,
            'deadline' => now()->toDateString(),
            'categorie_id' => $category->id,
            'year' => '2025',
        ]);
        // Minimal criteria to satisfy evidence FK
        $criteria = Criteria::create([
            'name' => 'C1',
            'sequence' => 1,
            'indicator_id' => $indicator->id,
        ]);

        Storage::fake('public');
        $storedPath = 'evidence/test.pdf';
        Storage::disk('public')->put($storedPath, 'x');

        $evidence = Evidence::create([
            'name' => 'Doc',
            'path' => [
                'files' => [[
                    'original_name' => 'doc.pdf',
                    'stored_name' => 'test.pdf',
                    'path' => $storedPath,
                    'size' => 10,
                ]],
            ],
            'type' => 'pdf',
            'detail' => null,
            'status' => true,
            'criteria_id' => $criteria->id,
            'user_id' => null,
        ]);

        return [$indicator, $evidence];
    }

    #[Test]
    public function super_admin_access_matrix(): void
    {
        [$indicator, $evidence] = $this->seedIndicatorAndEvidence();
        $u = User::factory()->create();
        $u->assignRole('super_admin');

        $this->actingAs($u, 'sanctum')->get('/dashboard/export')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/indicator')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/indicator/create')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get("/indicator/{$indicator->id}/show")->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/users')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/departments')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/categories')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/standards')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/settings')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/evidences')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/dashboardkpi')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get("/evidences/{$evidence->id}/download")->assertStatus(200);
    }

    #[Test]
    public function system_admin_access_matrix(): void
    {
        [$indicator, $evidence] = $this->seedIndicatorAndEvidence();
        $u = User::factory()->create();
        $u->assignRole('system_admin');

        $this->actingAs($u, 'sanctum')->get('/dashboard/export')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/indicator')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/indicator/create')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get("/indicator/{$indicator->id}/show")->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/users')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/departments')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/categories')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/standards')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/settings')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/evidences')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/dashboardkpi')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get("/evidences/{$evidence->id}/download")->assertStatus(200);
    }

    #[Test]
    public function qa_admin_access_matrix(): void
    {
        [$indicator, $evidence] = $this->seedIndicatorAndEvidence();
        $u = User::factory()->create();
        $u->assignRole('qa_admin');

        $this->actingAs($u, 'sanctum')->get('/dashboard/export')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/indicator')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/indicator/create')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get("/indicator/{$indicator->id}/show")->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/users')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/departments')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/categories')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/standards')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/settings')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/evidences')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get("/evidences/{$evidence->id}/download")->assertStatus(200);
    }

    #[Test]
    public function administration_admin_access_matrix(): void
    {
        [$indicator, $evidence] = $this->seedIndicatorAndEvidence();
        $u = User::factory()->create();
        $u->assignRole('administration_admin');

        $this->actingAs($u, 'sanctum')->get('/dashboard/export')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/indicator')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/indicator/create')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get("/indicator/{$indicator->id}/show")->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/users')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/departments')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/categories')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/standards')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/settings')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get('/evidences')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get("/evidences/{$evidence->id}/download")->assertStatus(200);
    }

    #[Test]
    public function normal_user_access_matrix(): void
    {
        [$indicator, $evidence] = $this->seedIndicatorAndEvidence();
        $u = User::factory()->create();
        $u->assignRole('user');
        Assignment::create([
            'indicator_id' => $indicator->id,
            'collector' => $u->id,
        ]);

        $this->actingAs($u, 'sanctum')->get('/dashboard')->assertStatus(403);
        $this->actingAs($u, 'sanctum')->get('/dashboard/export')->assertStatus(403);
        $this->actingAs($u, 'sanctum')->get('/indicator')->assertStatus(403);
        $this->actingAs($u, 'sanctum')->get('/indicator/create')->assertStatus(403);
        $this->actingAs($u, 'sanctum')->get("/indicator/{$indicator->id}/show")->assertStatus(403);
        $this->actingAs($u, 'sanctum')->get('/users')->assertStatus(403);
        $this->actingAs($u, 'sanctum')->get('/departments')->assertStatus(403);
        $this->actingAs($u, 'sanctum')->get('/categories')->assertStatus(403);
        $this->actingAs($u, 'sanctum')->get('/standards')->assertStatus(403);
        $this->actingAs($u, 'sanctum')->get('/settings')->assertStatus(403);
        // has evidence view + download permissions
        $this->actingAs($u, 'sanctum')->get('/evidences')->assertStatus(200);
        $this->actingAs($u, 'sanctum')->get("/evidences/{$evidence->id}/download")->assertStatus(200);
    }
}
