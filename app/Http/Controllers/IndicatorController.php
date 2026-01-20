<?php

namespace App\Http\Controllers;

use App\Http\Resources\IndicatorResource;
use App\Models\Category;
use App\Models\Criteria;
use App\Models\Department;
use App\Models\Affiliation;
use App\Models\Formula;
use App\Models\Indicator;
use App\Models\Standard;
use App\Models\User;
use App\Models\Variable;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class IndicatorController extends Controller
{
    public function index()
    {

        $indicators = Indicator::with([
            'category.standard',
            'assignments.user.department',
            'criterias',
            // 'evidences',
        ])
            ->get()
            // Database-agnostic ordering (SQLite-friendly):
            // 1) prefix rank by first 3 chars (NCS,NCO,NCP => 1,2,3 else 4)
            // 2) numeric part after first '-' if numeric; else 999999
            ->sortBy(function ($i) {
                $code = (string) ($i->code ?? '');
                $prefix = substr($code, 0, 3);
                $rank = match ($prefix) {
                    'NCS' => 1,
                    'NCO' => 2,
                    'NCP' => 3,
                    default => 4,
                };
                $num = 999999;
                $dashPos = strpos($code, '-');
                if ($dashPos !== false) {
                    $after = substr($code, $dashPos + 1);
                    if (preg_match('/^\d+$/', $after)) {
                        $num = (int) $after;
                    }
                }
                // Compose sortable key
                return sprintf('%02d-%06d-%s', $rank, $num, $code);
            })
            ->values()
            ->map(fn($i) => $this->serializeIndicatorForList($i));

        $years = Indicator::whereNotNull('year')
            ->selectRaw('DISTINCT year')
            ->orderBy('year', 'desc')
            ->pluck('year');
        // dd($indicators);

        return view('indicator.app', compact('indicators', 'years'));

        // return response()->json(['indicators' => $indicators]);
    }

    public function create()
    {
        $data = $this->formSelections();
        $data['criteriaOptions'] = [];

        // ใช้สำหรับ multi-select + filter
        $data['usersForAssign'] = User::select('id', 'first_name', 'last_name', 'department_id')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->display_name,
                'department_id' => $u->department_id,
            ]);

        return view('indicator.create', $data);
    }

    public function show(Request $request, $id)
    {
        $indicator = Indicator::with([
            'category.standard',
            'criterias',
            'variables',
            'formulas',
            'checklistItems',
            'assignments.user.department',
            'evidences',
        ])->findOrFail($id);

        // Prefer explicit request standard_id, otherwise fall back to the indicator's linked standard
        $standardId = $request->input('standard_id') ?: optional($indicator->category?->standard)->id;
        $data = $this->formSelections($standardId);

        // Reuse the API shape for the view
        $data = (new IndicatorResource($indicator))->toArray(request());

        return view('indicator.detail', compact('data'));

        // return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Basic
            'year' => 'required|integer|digits:4',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100',
            'max_score' => 'required|numeric|min:0',
            'standard_id' => 'required|exists:standards,id',
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(fn($q) => $q->where('standard_id', $request->input('standard_id'))),
            ],
            'type' => 'nullable|string',
            'deadline' => 'required|date',

            // Responsible
            'department_ids' => 'nullable|array',
            'department_ids.*' => 'exists:departments,id',

            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',

            // Rich text
            'description' => 'nullable|string',
            'condition' => 'nullable|string',
            'comment' => 'nullable|string',
            'annotation' => 'nullable|string',

            // Criteria
            'criteria' => 'nullable|array',
            'criteria.*.id' => 'nullable|integer|exists:criterias,id',
            'criteria.*.sequence' => 'required|integer',
            'criteria.*.name' => 'required|string',
            'criteria.*.description' => 'nullable|string',

            // Multi-choice (count-based)
            'multiCounts' => 'nullable|array',
            'multiCounts.*.count' => 'required|integer|min:0',
            'multiCounts.*.score' => 'required|numeric',

            // Multi-choice (selected-based)
            'multiSelected' => 'nullable|array',
            'multiSelected.*.sequence' => 'required|integer',
            'multiSelected.*.required_items' => 'nullable|array',
            'multiSelected.*.required_items.*' => 'integer',
            'multiSelected.*.score' => 'required|numeric',

            // Custom scoring
            'scoring.variables' => 'nullable|array',
            'scoring.variables.*.variable_name' => 'required|string|max:100',
            'scoring.variables.*.label_name' => 'required|string|max:100',
            'scoring.variables.*.type' => 'required|in:defined,input,output',
            'scoring.variables.*.value' => 'nullable|numeric',
            'scoring.condition' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $indicator = Indicator::create([
                'year' => $validated['year'],
                'name' => $validated['name'],
                'code' => $validated['code'],
                'max_score' => $validated['max_score'],
                'score_acc' => 0,
                'categorie_id' => $validated['category_id'], // คอลัมน์สะกดตามนี้
                'type' => $validated['type'] ?? null,
                'deadline' => $validated['deadline'],
                'status' => 0,
                'description' => $validated['description'] ?? null,
                'condition' => $validated['condition'] ?? null,
                'comment' => $validated['comment'] ?? null,
                'annotation' => $validated['annotation'] ?? null,
            ]);

            // สร้าง assignments หลายรายการ
            $indicator->assignments()->createMany(
                collect($validated['user_ids'])->unique()->values()->map(fn($uid) => ['collector' => $uid])->all()
            );

            // Notify assigned users about the new assignment
            try {
                $indicator->loadMissing(['assignments.collectorUser']);
                foreach ($indicator->assignments as $assignment) {
                    if ($assignment->collectorUser) {
                        $assignment->collectorUser->notify(new \App\Notifications\IndicatorAssignedNotification($indicator));
                    }
                }
            } catch (\Throwable $e) {
                // Fail silently for email issues; creation must succeed
            }

            $criteriaCount = $this->syncCriterias($indicator, $validated['criteria'] ?? []);
            $this->syncVariablesAndFormula($indicator, $validated['scoring'] ?? []);
            $this->syncChecklistFromSelected($indicator, $validated['multiSelected'] ?? []);
            // Generate checklist items from multiCounts via service
            app(\App\Services\ChecklistGenerator::class)
                ->syncFromCounts($indicator, $validated['multiCounts'] ?? [], $criteriaCount);

            DB::commit();

            return redirect()
                ->route('indicator.show', $indicator->id)
                ->with('success', 'ตัวบ่งชี้ถูกสร้างเรียบร้อยแล้ว');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // Manually notify all assignees from indicator detail page 

 



    public function edit($id)
    {
        $standardId = request('standard_id');

        $indicator = Indicator::with([
            'category.standard',
            'criterias',
            'variables',
            'formulas',
            'checklistItems',
            'assignments.user.department',
            'evidences',
        ])->findOrFail($id);

        $standardId = $standardId ?: optional($indicator->category?->standard)->id;
        $data = $this->formSelections($standardId);

        // ใช้สำหรับ multi-select + filter
        $data['usersForAssign'] = User::select('id', 'first_name', 'last_name', 'department_id')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->display_name,
                'department_id' => $u->department_id,
            ])
            ->values();

        // Reuse the API shape for the view
        $data_indicator = (new IndicatorResource($indicator))->toArray(request());

        return view('indicator.edit', [
            'data_indicator' => $data_indicator,
            'information' => $data,
        ]);
        // return response()->json(['information' => [$data], 'data_indicator' => $data_indicator]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            // Basic
            'year' => 'required|integer|digits:4',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100',
            'max_score' => 'required|numeric|min:0',
            'standard_id' => 'required|exists:standards,id',
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(fn($q) => $q->where('standard_id', $request->input('standard_id'))),
            ],
            'type' => 'nullable|string',
            'deadline' => 'required|date',

            // Responsible
            'department_ids' => 'nullable|array',
            'department_ids.*' => 'exists:departments,id',

            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',

            // Rich text
            'description' => 'nullable|string',
            'condition' => 'nullable|string',
            'comment' => 'nullable|string',
            'annotation' => 'nullable|string',

            // Criteria
            'criteria' => 'nullable|array',
            'criteria.*.id' => 'nullable|integer|exists:criterias,id',
            'criteria.*.sequence' => 'required|integer',
            'criteria.*.name' => 'required|string',
            'criteria.*.description' => 'nullable|string',

            // Multi-choice
            'multiCounts' => 'nullable|array',
            'multiCounts.*.count' => 'required|integer|min:0',
            'multiCounts.*.score' => 'required|numeric',

            'multiSelected' => 'nullable|array',
            'multiSelected.*.sequence' => 'required|integer',
            'multiSelected.*.required_items' => 'nullable|array',
            'multiSelected.*.required_items.*' => 'integer',
            'multiSelected.*.score' => 'required|numeric',

            // Scoring (variable/formula)
            'scoring.variables' => 'nullable|array',
            'scoring.variables.*.id' => 'nullable|integer|exists:variables,id',
            'scoring.variables.*.variable_name' => 'required|string|max:100',
            'scoring.variables.*.label_name' => 'required|string|max:100',
            'scoring.variables.*.type' => 'required|in:defined,input,output',
            'scoring.variables.*.value' => 'nullable|numeric',
            'scoring.condition' => 'nullable|string',
            'scoring.formula_id' => 'nullable|integer|exists:formulas,id',
        ]);

        try {
            DB::transaction(function () use ($validated, $id) {
                $indicator = Indicator::findOrFail($id);

                // --- Update base fields ---
                $indicator->update([
                    'year' => $validated['year'],
                    'name' => $validated['name'],
                    'code' => $validated['code'],
                    'max_score' => $validated['max_score'],
                    'categorie_id' => $validated['category_id'],     // Database column is categorie_id
                    'type' => $validated['type'] ?? null,
                    'deadline' => $validated['deadline'],
                    'description' => $validated['description'] ?? null,
                    'condition' => $validated['condition'] ?? null,
                    'comment' => $validated['comment'] ?? null,
                    'annotation' => $validated['annotation'] ?? null,
                ]);

                // --- Departments (you validated them, so sync them) ---
                if (method_exists($indicator, 'departments')) {
                    $indicator->departments()->sync($validated['department_ids'] ?? []);
                }

                // --- Assignments (delete & recreate) ---
                $indicator->assignments()->delete();

                $userIds = collect($validated['user_ids'])->unique()->values();
                $indicator->assignments()->createMany(
                    $userIds->map(function ($uid) {
                        return ['collector' => $uid];
                    })->all()
                );

                // --- Criteria (update existing by ID or create new) ---
                $criteriaCount = $this->syncCriteriasWithUpdate($indicator, $validated['criteria'] ?? []);

                // --- Variable & Formula (delete all existing and create new) ---
                $this->deleteScoringData($indicator);
                $this->syncVariablesAndFormula($indicator, $validated['scoring'] ?? []);

                // --- Checklist (delete all existing and create new) ---
                $indicator->checklistItems()->delete();
                $this->syncChecklistFromSelected($indicator, $validated['multiSelected'] ?? []);
                app(\App\Services\ChecklistGenerator::class)
                    ->syncFromCounts($indicator, $validated['multiCounts'] ?? [], $criteriaCount);
            });

            return redirect()
                ->route('indicator.show', $id)
                ->with('success', 'ตัวบ่งชี้ถูกอัปเดตเรียบร้อยแล้ว');
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการอัปเดต: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $indicator = Indicator::findOrFail($id);
            $indicator->delete();

            DB::commit();

            return redirect()
                ->route('indicator.index')
                ->with('success', 'ตัวบ่งชี้ถูกลบเรียบร้อยแล้ว');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'เกิดข้อผิดพลาดในการลบ: ' . $e->getMessage()]);
        }
    }

    public function getIndicatorsByCategory($categoryId)
    {
        // หมายเหตุ: คอลัมน์คือ categorie_id
        $indicators = Indicator::where('categorie_id', $categoryId)->get();

        return response()->json(['indicators' => $indicators]);
    }

    public function getIndicatorsByStandard($standardId)
    {
        $indicators = Indicator::whereHas('category.standard', function ($q) use ($standardId) {
            $q->where('id', $standardId);
        })->get();

        return response()->json(['indicators' => $indicators]);
    }

    /* ===========================
     * Private helpers
     * =========================== */

    private function formSelections(?int $standardId = null): array
    {
        $categoriesByStandard = Category::query()
            ->orderBy('name')
            ->get()
            ->groupBy('standard_id')
            ->map(function ($items) {
                return $items->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->values();
            })
            ->toArray();

        return [
            'standards' => Standard::query()->pluck('name', 'id')->toArray(),
            'categories' => Category::query()
                ->when($standardId, fn($q) => $q->where('standard_id', $standardId))
                ->pluck('name', 'id')
                ->toArray(),
            'departments' => Department::query()
                ->select('id', 'name', 'work_group')
                ->orderBy('name')
                ->get()
                ->map(fn($d) => [
                    'id' => $d->id,
                    'name' => $d->name,
                    'work_group' => $d->work_group,
                ])
                ->toArray(),
            'affiliations' => Affiliation::query()->orderBy('name')->pluck('name', 'name')->toArray(),
            'categoriesByStandard' => $categoriesByStandard,
        ];
    }

    private function serializeIndicatorForList(Indicator $i): array
    {
        // รองรับทั้ง cast ที่เป็น Carbon และสตริงธรรมดา
        $deadline = null;
        if ($i->deadline instanceof Carbon) {
            $deadline = $i->deadline->format('Y-m-d');
        } elseif (! empty($i->deadline)) {
            try {
                $deadline = Carbon::parse($i->deadline)->format('Y-m-d');
            } catch (\Throwable $e) {
                $deadline = (string) $i->deadline;
            }
        }

        // Aggregate criteria status:
        // - If any criteria has status 0 => overall 0 (รอดำเนินการ)
        // - Else if any criteria has status 2 => overall 2 (เอกสารไม่ครบถ้วน)
        // - Else => 1 (เอกสารครบถ้วน)
        $hasPending = $i->criterias->contains(fn($c) => (int) ($c->status ?? -1) === 0);
        $hasIncomplete = $i->criterias->contains(fn($c) => (int) ($c->status ?? -1) === 2);
        $criteriaStatus = $hasPending ? 0 : ($hasIncomplete ? 2 : 1);

        return [
            'id' => $i->id,
            'name' => $i->name,
            'year' => $i->year,
            'code' => $i->code,
            'deadline' => $deadline,
            'status' => $i->status,
            // Aggregated status from criterias
            'criteria_status' => $criteriaStatus,
            'score_acc' => $i->score_acc,
            'max_score' => $i->max_score,
            'type' => $i->type,
            'category' => $i->category ? [
                'id' => $i->category->id,
                'name' => $i->category->name,
            ] : null,
            'standard' => ($i->category && $i->category->standard) ? [
                'id' => $i->category->standard->id,
                'name' => $i->category->standard->name,
            ] : null,

            // ทำให้ dashboard.blade เอาไป map หา department ได้
            'assignments' => $i->assignments->map(function ($a) {
                // ถ้าในความสัมพันธ์ Assignment มี ->user ก็ใช้เลย ไม่งั้น fallback หาเอง
                $user = $a->user ?? \App\Models\User::find($a->collector);

                return [
                    'user' => $user ? [
                        'id' => $user->id,
                        'name' => $user->name,
                        'department_id' => $user->department_id,
                        'department_name' => optional($user->department)->name,
                        'work_group_name' => optional($user->department)->work_group,
                    ] : null,
                ];
            }),

            'criteria' => $i->criterias->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'description' => $c->description,
                    'sequence' => $c->sequence,
                    'status' => $c->status,
                ];
            }),

            // 'evidences' => $i->evidences->map(function ($e) {
            //     return [
            //         'id'         => $e->id,
            //         'name'       => $e->name,
            //         'path'       => $e->path,
            //         'created_at' => $e->created_at instanceof Carbon
            //             ? $e->created_at->format('Y-m-d H:i:s')
            //             : (string) $e->created_at,
            //     ];
            // }),
        ];
    }

    /**
     * Insert criteria rows; return count for combination generation.
     */
    private function syncCriterias(Indicator $indicator, array $criteria): int
    {
        $rows = [];
        foreach ($criteria as $c) {
            $rows[] = [
                'name' => (string) ($c['name'] ?? ''),
                'description' => $c['description'] ?? null,
                'sequence' => (int) ($c['sequence'] ?? 0),
                'indicator_id' => $indicator->id,
            ];
        }
        if (! empty($rows)) {
            // ถ้าต้องการ timestamps ให้เพิ่ม created_at/updated_at เอง หรือใช้ createMany ผ่าน relation
            Criteria::insert($rows);
        }

        return count($rows);
    }

    /**
     * Variables + a single Formula (text in formulas.condition); link via pivot.
     */
    private function syncVariablesAndFormula(Indicator $indicator, array $scoring): void
    {
        $indicator->variables()->delete();
        $indicator->formulas()->delete();

        $vars = (array) ($scoring['variables'] ?? []);
        $formulaText = trim((string) ($scoring['condition'] ?? ''));

        $createdVars = [];
        foreach ($vars as $v) {
            $name = trim((string) ($v['variable_name'] ?? ''));
            $label = trim((string) ($v['label_name'] ?? ''));
            $type = (string) ($v['type'] ?? 'defined'); // defined|input|output
            $value = array_key_exists('value', $v) ? $v['value'] : null;

            if ($name === '' || $label === '') {
                continue; // Skip if either name or label is empty
            }

            $createdVars[] = $indicator->variables()->create([
                'variable_name' => $name,
                'label_name' => $label,
                'type' => $type,
                'value' => $type === 'defined' ? $value : null,
            ]);
        }

        if ($formulaText !== '') {
            $formula = $indicator->formulas()->create([
                'condition' => $formulaText,
            ]);

            foreach ($createdVars as $var) {
                $var->formulas()->attach($formula->id);
            }
        }
    }

    /**
     * Persist explicit checklist combos.
     */
    private function syncChecklistFromSelected(Indicator $indicator, array $multiSelected): void
    {
        foreach ($multiSelected as $row) {
            $req = array_values(array_filter((array) ($row['required_items'] ?? []), 'is_numeric'));
            $score = (float) ($row['score'] ?? 0);
            $sequence = (int) ($row['sequence'] ?? 1);

            if (! $req) {
                continue;
            }

            sort($req);

            $indicator->checklistItems()->create([
                'required_items' => $req,
                'score' => $score,
                'sequence' => $sequence,
            ]);
        }
    }

    /**
     * From count rules like [{count:1,score:5},{count:2,score:15}],
     * generate all k-combinations of [1..criteriaCount], skipping duplicates.
     */
    private function syncChecklistFromCounts(Indicator $indicator, array $multiCounts, int $criteriaCount): void
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
            })->flip();

        $universe = range(1, $criteriaCount);

        foreach ($multiCounts as $rule) {
            $k = (int) ($rule['count'] ?? 0);
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
                    'score' => $score,
                    'sequence' => 1,
                ]);
                $existingKeys[$key] = true;
            }
        }
    }

    /** Generate all k-combinations of an integer array */
    private function kCombinations(array $arr, int $k): array
    {
        $n = count($arr);
        if ($k < 0 || $k > $n) {
            return [];
        }
        if ($k === 0) {
            return [[]];
        }

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

    /**
     * Update existing criteria by ID or create new ones.
     * Returns count of criteria for combination generation.
     */
    private function syncCriteriasWithUpdate(Indicator $indicator, array $criteria): int
    {
        $existingIds = [];
        $totalCount = 0;

        foreach ($criteria as $c) {
            $criteriaData = [
                'name' => (string) ($c['name'] ?? ''),
                'description' => $c['description'] ?? null,
                'sequence' => (int) ($c['sequence'] ?? 0),
                'indicator_id' => $indicator->id,
            ];

            if (! empty($c['id'])) {
                // Update existing criteria
                $existingCriteria = Criteria::where('id', $c['id'])
                    ->where('indicator_id', $indicator->id)
                    ->first();

                if ($existingCriteria) {
                    $existingCriteria->update($criteriaData);
                    $existingIds[] = $c['id'];
                    $totalCount++;
                }
            } else {
                // Create new criteria
                $newCriteria = Criteria::create($criteriaData);
                $existingIds[] = $newCriteria->id;
                $totalCount++;
            }
        }

        // Delete criteria that are not in the submitted form (orphaned criteria)
        $indicator->criterias()->whereNotIn('id', $existingIds)->delete();

        return $totalCount;
    }

    /**
     * Delete all scoring-related data for an indicator.
     * This includes variables, formulas, and their pivot relationships.
     */
    private function deleteScoringData(Indicator $indicator): void
    {
        // Delete variable-formula pivot relationships first
        $indicator->variables()->each(function ($variable) {
            $variable->formulas()->detach();
        });

        // Delete variables and formulas
        $indicator->variables()->delete();
        $indicator->formulas()->delete();
    }
}
