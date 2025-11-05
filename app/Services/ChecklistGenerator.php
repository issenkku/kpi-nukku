<?php

namespace App\Services;

use App\Models\Indicator;

class ChecklistGenerator
{
    public function syncFromCounts(Indicator $indicator, array $multiCounts, int $criteriaCount): void
    {
        if ($criteriaCount <= 0 || empty($multiCounts)) {
            return;
        }

        $existingKeys = $indicator->checklistItems()
            ->get(['required_items'])
            ->pluck('required_items')
            ->map(function ($arr) {
                $a = array_map('intval', (array) $arr);
                sort($a);
                return implode(',', $a);
            })
            ->flip();

        $universe = range(1, $criteriaCount);

        foreach ($multiCounts as $rule) {
            $k     = (int) ($rule['count'] ?? 0);
            $score = (float) ($rule['score'] ?? 0);

            if ($k <= 0 || $k > $criteriaCount) {
                continue;
            }

            foreach ($this->kCombinations($universe, $k) as $combo) {
                $key = implode(',', $combo);
                if (isset($existingKeys[$key])) {
                    continue;
                }

                $indicator->checklistItems()->create([
                    'required_items' => $combo,
                    'score'          => $score,
                    'sequence'       => 1,
                ]);
                $existingKeys[$key] = true;
            }
        }
    }

    public function kCombinations(array $arr, int $k): array
    {
        $n = count($arr);
        if ($k < 0 || $k > $n) return [];
        if ($k === 0) return [[]];

        $out = [];
        $this->kCombDfs($arr, $k, 0, [], $out);
        return $out;
    }

    private function kCombDfs(array $arr, int $k, int $start, array $curr, array &$out): void
    {
        if (count($curr) === $k) {
            $tmp = $curr;
            sort($tmp);
            $out[] = $tmp;
            return;
        }
        for ($i = $start; $i < count($arr); $i++) {
            $curr[] = (int) $arr[$i];
            $this->kCombDfs($arr, $k, $i + 1, $curr, $out);
            array_pop($curr);
        }
    }
}

