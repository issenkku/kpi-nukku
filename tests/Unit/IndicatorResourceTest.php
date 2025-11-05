<?php

namespace Tests\Unit;

use App\Http\Resources\IndicatorResource;
use App\Models\Assignment;
use App\Models\Category;
use App\Models\Criteria;
use App\Models\Indicator;
use App\Models\Standard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class IndicatorResourceTest extends TestCase
{
    use RefreshDatabase;

    private function makeIndicatorGraph(): Indicator
    {
        $standard = Standard::create(['name' => 'STD']);
        $category = Category::create(['name' => 'CAT', 'standard_id' => $standard->id, 'max_score' => 100]);
        $indicator = Indicator::create([
            'name' => 'My Indicator',
            'code' => 'IND-999',
            'max_score' => 100,
            'score_acc' => 0,
            'status' => 1,
            'deadline' => now()->toDateString(),
            'categorie_id' => $category->id,
            'year' => '2025',
        ]);
        Criteria::create(['name' => 'C1', 'sequence' => 1, 'indicator_id' => $indicator->id]);
        Criteria::create(['name' => 'C2', 'sequence' => 2, 'indicator_id' => $indicator->id]);

        // Two users in different departments
        $u1 = User::factory()->create();
        $u2 = User::factory()->create();
        Assignment::create(['indicator_id' => $indicator->id, 'collector' => $u1->id]);
        Assignment::create(['indicator_id' => $indicator->id, 'collector' => $u2->id]);

        return $indicator->load(['category.standard', 'criterias', 'assignments.user.department']);
    }

    #[Test]
    public function maps_core_fields_and_relations(): void
    {
        $indicator = $this->makeIndicatorGraph();
        $payload = (new IndicatorResource($indicator))->response()->getData(true);
        $res = $payload['data'];

        $this->assertSame('IND-999', $res['code']);
        $this->assertSame('2025', $res['year']);
        $this->assertArrayHasKey('category', $res);
        $this->assertArrayHasKey('standard', $res);
        $this->assertIsArray($res['criterias']);
        $this->assertCount(2, $res['criterias']);
        $this->assertIsArray($res['departments']);
        $this->assertGreaterThanOrEqual(1, count($res['departments']));
        $this->assertEquals(date('Y-m-d'), $res['deadline']);
    }
}
