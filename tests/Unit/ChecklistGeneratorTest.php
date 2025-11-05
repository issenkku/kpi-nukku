<?php

namespace Tests\Unit;

use App\Models\Indicator;
use App\Models\Standard;
use App\Models\Category;
use App\Models\Criteria;
use App\Services\ChecklistGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ChecklistGeneratorTest extends TestCase
{
    use RefreshDatabase;

    private function makeIndicatorWithCriterias(int $n): Indicator
    {
        $standard = Standard::create(['name' => 'STD']);
        $category = Category::create(['name' => 'CAT', 'standard_id' => $standard->id, 'max_score' => 100]);
        $indicator = Indicator::create([
            'name' => 'X', 'code' => 'X-1', 'max_score' => 100, 'score_acc' => 0,
            'status' => 1, 'deadline' => now()->toDateString(), 'categorie_id' => $category->id,
            'year' => '2025',
        ]);
        for ($i = 1; $i <= $n; $i++) {
            Criteria::create(['name' => 'C'.$i, 'sequence' => $i, 'indicator_id' => $indicator->id]);
        }
        return $indicator;
    }

    #[Test]
    public function k_combinations_basic(): void
    {
        $svc = new ChecklistGenerator();
        $combos = $svc->kCombinations([1,2,3], 2);
        sort($combos);
        $this->assertSame([[1,2],[1,3],[2,3]], $combos);
    }

    #[Test]
    public function sync_from_counts_creates_expected_items_without_duplicates(): void
    {
        $svc = new ChecklistGenerator();
        $indicator = $this->makeIndicatorWithCriterias(3);

        $svc->syncFromCounts($indicator, [['count' => 2, 'score' => 5]], 3);
        $this->assertEquals(3, $indicator->checklistItems()->count());

        // Calling again does not duplicate
        $svc->syncFromCounts($indicator, [['count' => 2, 'score' => 5]], 3);
        $this->assertEquals(3, $indicator->checklistItems()->count());
    }
}

