<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Criteria;
use App\Models\Evidence;
use App\Models\Indicator;
use App\Models\Standard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EvidenceTest extends TestCase
{
    use RefreshDatabase;

    private function grant(User $user, string $permission): void
    {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
    }

    private function seedChain(): Criteria
    {
        $standard = Standard::create(['name' => 'STD']);
        $category = Category::create(['name' => 'CAT', 'standard_id' => $standard->id, 'max_score' => 10]);
        $indicator = Indicator::create([
            'name' => 'IND',
            'code' => 'T-001',
            'max_score' => 10,
            'score_acc' => 0,
            'status' => 1,
            'deadline' => now()->toDateString(),
            'categorie_id' => $category->id,
            'year' => '2025',
        ]);
        return Criteria::create([
            'name' => 'C1',
            'sequence' => 1,
            'indicator_id' => $indicator->id,
        ]);
    }

    #[Test]
    public function index_requires_view_permission(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')->get('/evidences')->assertStatus(403);
        $this->grant($user, 'view-evidence');
        $this->actingAs($user, 'sanctum')->get('/evidences')->assertStatus(200);
    }

    #[Test]
    public function can_upload_file_evidence_with_permission(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->grant($user, 'view-evidence');
        $this->grant($user, 'create-evidence');
        $criteria = $this->seedChain();

        $file = UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf');

        $resp = $this->actingAs($user, 'sanctum')
            ->post('/evidences/store', [
                'criteria_id' => $criteria->id,
                'files' => [$file],
            ]);

        $resp->assertStatus(302);
        $this->assertDatabaseCount('evidence', 1);
        $this->assertEquals(1, Evidence::count());

        $e = Evidence::first();
        $this->assertEquals($criteria->id, $e->criteria_id);
        $this->assertEquals('pdf', $e->type);
        $this->assertIsArray($e->path);
        $this->assertArrayHasKey('files', $e->path);
        $this->assertNotEmpty($e->path['files']);
        $this->assertTrue(Storage::disk('public')->exists($e->path['files'][0]['path']));
    }

    #[Test]
    public function can_download_uploaded_file_with_permission(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->grant($user, 'view-evidence');
        $this->grant($user, 'create-evidence');
        $this->grant($user, 'download-evidence');
        $criteria = $this->seedChain();

        // Upload
        $file = UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf');
        $this->actingAs($user, 'sanctum')->post('/evidences/store', [
            'criteria_id' => $criteria->id,
            'files' => [$file],
        ])->assertStatus(302);

        $e = Evidence::firstOrFail();

        // Download
        $res = $this->actingAs($user, 'sanctum')->get("/evidences/{$e->id}/download");
        $res->assertStatus(200);
        $this->assertTrue($res->headers->has('content-disposition'));
    }

    #[Test]
    public function toggle_status_requires_edit_permission(): void
    {
        $user = User::factory()->create();
        $this->grant($user, 'view-evidence');
        $this->grant($user, 'create-evidence');
        $criteria = $this->seedChain();

        // Create a note evidence (no file)
        $evidence = Evidence::create([
            'name' => 'Note',
            'path' => [],
            'type' => 'note',
            'detail' => 'x',
            'status' => true,
            'criteria_id' => $criteria->id,
            'user_id' => $user->id,
        ]);

        // Without permission -> 403 (route protected by edit-evidence)
        $this->actingAs($user, 'sanctum')
            ->patch("/evidences/{$evidence->id}/toggle-status")
            ->assertStatus(403);

        // With permission -> 200 and status flipped
        $this->grant($user, 'edit-evidence');
        $res = $this->actingAs($user, 'sanctum')
            ->patch("/evidences/{$evidence->id}/toggle-status");
        $res->assertStatus(200);
        $evidence->refresh();
        $this->assertFalse($evidence->status);
    }
}
