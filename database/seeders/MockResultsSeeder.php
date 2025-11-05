<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Indicator;
use App\Models\Criteria;

class MockResultsSeeder extends Seeder
{
    private function realignSequence(string $table, string $column = 'id'): void
    {
        try {
            if (DB::getDriverName() === 'pgsql') {
                // Canonical sequence realignment: set last_value = MAX(id), is_called = true
                DB::statement(
                    "SELECT setval(pg_get_serial_sequence('" . $table . "','" . $column . "'), COALESCE((SELECT MAX(" . $column . ") FROM " . $table . "), 0), true)"
                );
            }
        } catch (\Throwable $e) {
            // no-op: if sequence doesn't exist or DB is not pgsql
        }
    }

    public function run(): void
    {
        // Make sure sequences are aligned before inserting rows (PostgreSQL)
        $this->realignSequence('indicators');
        $this->realignSequence('criterias');
        $this->realignSequence('evidence');

        // Target indicator codes to mock across multiple years
        $targetCodes = [
            'NCS-9', 'NCP-2', 'NCO-8', 'NCS-12', 'NCS-2',
        ];

        // Years to mock
        $years = [2022, 2023, 2024, 2025];

        $baseIndicators = Indicator::query()
            ->whereIn('code', $targetCodes)
            ->get()
            ->keyBy('code');

        if ($baseIndicators->isEmpty()) {
            // Fallback: take a few indicators that have a year defined
            $baseIndicators = Indicator::query()
                ->whereNotNull('year')
                ->orderBy('id')
                ->limit(8)
                ->get()
                ->keyBy('code');
        }

        foreach ($baseIndicators as $code => $base) {
            foreach ($years as $year) {
                // Get or create indicator for this code+year
                $ind = Indicator::query()
                    ->where('code', $code)
                    ->where('year', (string) $year)
                    ->first();

                if (!$ind) {
                    // Clone from base with adjusted year and deadline
                    $deadline = sprintf('%d-12-31', (int) $year);
                    // Realign again just before insert, in case other seeds ran
                    $this->realignSequence('indicators');
                    $id = DB::table('indicators')->insertGetId([
                        'name'         => $base->name,
                        'year'         => (string) $year,
                        'code'         => $code,
                        'type'         => $base->type,
                        'description'  => $base->description,
                        'condition'    => $base->condition,
                        'annotation'   => $base->annotation,
                        'deadline'     => $deadline,
                        'status'       => $base->status ?? 0,
                        'comment'      => $base->comment,
                        'score_acc'    => null,
                        'max_score'    => $base->max_score ?? 10.0,
                        'categorie_id' => $base->categorie_id,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                    $ind = Indicator::find($id);
                }

                // Compute a plausible score between 60% and 95% of max, vary by year
                $max = (float) ($ind->max_score ?? $base->max_score ?? 10.0);
                $seed = crc32($code . '|' . $year);
                mt_srand($seed);
                $ratio = mt_rand(60, 95) / 100; // 0.60 - 0.95
                $score = round($max * $ratio, 2);

                DB::table('indicators')
                    ->where('id', $ind->id)
                    ->update(['score_acc' => $score, 'updated_at' => now()]);

                // Ensure at least one assignment exists so it appears in dashboards
                $collectorId = 1;
                $exists = DB::table('assignments')
                    ->where('indicator_id', $ind->id)
                    ->exists();

                if (!$exists) {
                    DB::table('assignments')->insert([
                        'indicator_id' => $ind->id,
                        'collector'    => $collectorId,
                    ]);
                }

                // Ensure criterias exist for this indicator to avoid Blade undefined variable errors
                $hasCriterias = Criteria::where('indicator_id', $ind->id)->exists();
                if (!$hasCriterias) {
                    $baseCriterias = Criteria::where('indicator_id', $base->id)
                        ->orderBy('sequence')
                        ->get(['name', 'description', 'sequence']);

                    if ($baseCriterias->isEmpty()) {
                        // Create a minimal default criteria
                        $this->realignSequence('criterias');
                        DB::table('criterias')->insert([
                            'name'         => 'หลักฐานประกอบตัวบ่งชี้',
                            'description'  => 'เกณฑ์ตัวอย่างสำหรับการทดสอบการอัปโหลดหลักฐาน',
                            'sequence'     => 1,
                            'indicator_id' => $ind->id,
                            'status'       => 0,
                        ]);
                    } else {
                        // Clone from base indicator
                        foreach ($baseCriterias as $c) {
                            $this->realignSequence('criterias');
                            DB::table('criterias')->insert([
                                'name'         => $c->name,
                                'description'  => $c->description,
                                'sequence'     => (int) $c->sequence,
                                'indicator_id' => $ind->id,
                                'status'       => 0,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
