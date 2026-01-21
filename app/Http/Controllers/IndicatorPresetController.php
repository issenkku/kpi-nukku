<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use App\Models\Standard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndicatorPresetController extends Controller
{

    public function index()
    {
        $indicators = Indicator::with([
            'category.standard',
            'criterias'
        ])->orderBy('id')->get();


        $standards = Standard::orderBy('id')->get();

        return view('indicator.app', compact('indicators', 'standards'));
    }


    // ✅ Export Indicator เป็น JSON
    public function export($id)
    {
        $indicator = Indicator::with([
            'criterias.evidenceRequirements',
            'variables',
            'formulas.variables',
            'checklistItems',
        ])->findOrFail($id);

        $data = [
            'name'         => $indicator->name,
            'code'         => $indicator->code,
            'description'  => $indicator->description,
            'condition'    => $indicator->condition,
            'annotation'   => $indicator->annotation,
            'deadline'     => $indicator->deadline?->format('Y-m-d'),
            'max_score'    => $indicator->max_score,
            'comment'      => $indicator->comment,
            'type'         => $indicator->type,
            'categorie_id' => $indicator->categorie_id,

            'criterias' => $indicator->criterias->map(function ($c) {
                return [
                    'name'        => $c->name,
                    'description' => $c->description,
                    'sequence'    => $c->sequence,
                    'required_evidence_total' => $c->required_evidence_total,
                    'evidence_requirements' => $c->evidenceRequirements
                        ->sortBy('sequence')
                        ->map(function ($req) {
                            return [
                                'name' => $req->name,
                                'sequence' => $req->sequence,
                            ];
                        })
                        ->values(),
                ];
            }),

            'variables' => $indicator->variables->map(function ($v) {
                return [
                    'variable_name' => $v->variable_name,
                    'label_name'    => $v->label_name,
                    'type'          => $v->type,
                    'value'         => $v->value,

                ];
            }),

            'formulas' => $indicator->formulas->map(function ($f) {
                return [
                    'condition' => $f->condition,
                    'variables' => $f->variables->map(function ($v) {
                        return ['label_name' => $v->label_name]; // ใช้ชื่อแทน id
                    }),
                ];
            }),

            'checklist_items' => $indicator->checklistItems->map(function ($cl) {
                return [
                    'required_items' => $cl->required_items,
                    'score'          => $cl->score,
                    'description'    => $cl->description,
                ];
            }),
        ];

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="indicator_preset_' . $id . '.json"',
            'Content-Type'        => 'application/json',
        ]);
    }



    // ✅ Import Indicator จาก JSON
    public function import(Request $request)
    {
        $request->validate([
            'preset_file' => 'required|file|mimes:json,txt',
            'year'        => 'required|integer|min:2000|max:2100',
        ]);

        $json = json_decode(file_get_contents($request->file('preset_file')), true);

        if (!is_array($json)) {
            return back()->with('error', 'Invalid JSON structure.');
        }

        // ✅ ถ้าเป็น single object → ห่อเป็น array
        if (array_keys($json) !== range(0, count($json) - 1)) {
            $json = [$json];
        }

        $yy = (int) $request->input('year');

        \DB::transaction(function () use ($json, $yy) {
            // Align PostgreSQL sequences
            try {
                DB::statement("SELECT setval(pg_get_serial_sequence('indicators','id'), COALESCE((SELECT MAX(id) FROM indicators), 0) + 1, false)");
                DB::statement("SELECT setval(pg_get_serial_sequence('criterias','id'), COALESCE((SELECT MAX(id) FROM criterias), 0) + 1, false)");
                DB::statement("SELECT setval(pg_get_serial_sequence('variables','id'), COALESCE((SELECT MAX(id) FROM variables), 0) + 1, false)");
                DB::statement("SELECT setval(pg_get_serial_sequence('formulas','id'), COALESCE((SELECT MAX(id) FROM formulas), 0) + 1, false)");
                DB::statement("SELECT setval(pg_get_serial_sequence('checklist_items','id'), COALESCE((SELECT MAX(id) FROM checklist_items), 0) + 1, false)");
            } catch (\Throwable $e) {
                report($e);
                return back()->with('error', 'Import failed: ' . $e->getMessage());
            }

            foreach ($json as $data) {
                $missing = [];
                foreach (['name', 'code', 'categorie_id'] as $field) {
                    if (empty($data[$field])) {
                        $missing[] = $field;
                    }
                }
                if (!empty($missing)) {
                    throw new \Exception('Import failed: ขาดฟิลด์ที่จำเป็น: ' . implode(', ', $missing));
                }

                // ถ้า deadline ไม่มี → กำหนดเป็นสิ้นปี
                if (empty($data['deadline'])) {
                    $data['deadline'] = sprintf('%04d-12-31', $yy);
                }

                // 1. Indicator
                unset($data['id']);
                $indicator = Indicator::create([
                    'name'         => $data['name'] ?? null,
                    'year'         => $yy,
                    'code'         => $data['code'] ?? null,
                    'description'  => $data['description'] ?? null,
                    'condition'    => $data['condition'] ?? null,
                    'annotation'   => $data['annotation'] ?? null,
                    'deadline'     => $data['deadline'] ?? null,
                    'status'       => 0,
                    'comment'      => $data['comment'] ?? null,
                    'score_acc'    => 0,
                    'max_score'    => $data['max_score'] ?? 0,
                    'type'         => $data['type'] ?? null,
                    'categorie_id' => $data['categorie_id'] ?? null,
                ]);

                // 2. Criterias
                foreach ($data['criterias'] ?? [] as $c) {
                    $criteria = $indicator->criterias()->create([
                        'name'        => $c['name'] ?? null,
                        'description' => $c['description'] ?? null,
                        'sequence'    => $c['sequence'] ?? 0,
                        'required_evidence_total' => $c['required_evidence_total'] ?? null,
                        
                    ]);
                    foreach ($c['evidence_requirements'] ?? [] as $req) {
                        $name = trim((string) ($req['name'] ?? ''));
                        if ($name === '') {
                            continue;
                        }
                        $criteria->evidenceRequirements()->create([
                            'name' => $name,
                            'sequence' => (int) ($req['sequence'] ?? 1),
                        ]);
                    }
                }

                // 3. Variables
                $variablesMap = [];
                foreach ($data['variables'] ?? [] as $v) {
                    $variable = $indicator->variables()->create([
                        'variable_name' => $v['variable_name'] ?? null,
                        'label_name'    => $v['label_name'] ?? null,
                        'type'          => $v['type'] ?? 'number',
                        'value'         => $v['value'] ?? 0,
                    ]);
                    $variablesMap[$v['label_name']] = $variable->id;
                }

                // 4. Formulas
                foreach ($data['formulas'] ?? [] as $f) {
                    $formula = $indicator->formulas()->create([
                        'condition' => $f['condition'] ?? null,
                    ]);
                    foreach ($f['variables'] ?? [] as $var) {
                        $label = $var['label_name'] ?? null;
                        if ($label && isset($variablesMap[$label])) {
                            $formula->variables()->attach($variablesMap[$label]);
                        }
                    }
                }

                // 5. Checklist
                foreach ($data['checklist_items'] ?? [] as $cl) {
                    $indicator->checklistItems()->create([
                        'required_items' => $cl['required_items'] ?? [],
                        'score'          => $cl['score'] ?? 0,
                        'description'    => $cl['description'] ?? null,
                    ]);
                }
            }
        });

        return back()->with('success', 'Preset imported successfully!');
    }

    public function exportBulk(Request $request)
    {
        $ids = (array) $request->input('ids', []);
        $year = $request->input('year');

        $query = Indicator::with([
            'criterias.evidenceRequirements',
            'variables',
            'formulas.variables',
            'checklistItems',
        ]);

        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        }
        if (!empty($year)) {
            $query->where('year', (int) $year);
        }

        $indicators = $query->get();

        $data = $indicators->map(function ($indicator) {
            return [
                'name'         => $indicator->name,
                'code'         => $indicator->code,
                'description'  => $indicator->description,
                'condition'    => $indicator->condition,
                'annotation'   => $indicator->annotation,
                'deadline'     => $indicator->deadline?->format('Y-m-d'),
                'max_score'    => $indicator->max_score,
                'type'         => $indicator->type,
                'categorie_id' => $indicator->categorie_id,
                'criterias'    => $indicator->criterias->map(fn($c) => [
                    'name'        => $c->name,
                    'description' => $c->description,
                    'sequence'    => $c->sequence,
                    'required_evidence_total' => $c->required_evidence_total,
                    'evidence_requirements' => $c->evidenceRequirements
                        ->sortBy('sequence')
                        ->map(fn($req) => [
                            'name' => $req->name,
                            'sequence' => $req->sequence,
                        ])
                        ->values(),
                ]),
                'variables'    => $indicator->variables->map(fn($v) => [
                    'variable_name' => $v->variable_name,
                    'label_name'    => $v->label_name,
                    'type'          => $v->type,
                    'value'         => $v->value,
                ]),
                'formulas'     => $indicator->formulas->map(fn($f) => [
                    'condition' => $f->condition,
                    'variables' => $f->variables->map(fn($v) => ['label_name' => $v->label_name]),
                ]),
                'checklist_items' => $indicator->checklistItems->map(fn($cl) => [
                    'required_items' => $cl->required_items,
                    'score'          => $cl->score,
                ]),
            ];
        });

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="indicators_preset.json"',
            'Content-Type'        => 'application/json',
        ]);
    }

    /**
     * Duplicate selected indicators (only selected items) into a target year.
     */
    public function duplicate(Request $request)
    {
        $validated = $request->validate([
            'ids'         => 'required|array|min:1',
            'ids.*'       => 'integer',
            'target_year' => 'required|integer|min:2000|max:2100',
        ]);

        $ids        = (array) $validated['ids'];
        $targetYear = (int) $validated['target_year'];

        $indicators = Indicator::with([
                'criterias.evidenceRequirements',
                'variables',
                'formulas.variables',
                'checklistItems',
            ])->whereIn('id', $ids)
            ->get();

        if ($indicators->isEmpty()) {
            return back()->with('error', 'No indicators found to duplicate.');
        }

        \DB::transaction(function () use ($indicators, $targetYear) {
            // Align PostgreSQL sequences (safe to attempt; ignored by other drivers)
            try {
                DB::statement("SELECT setval(pg_get_serial_sequence('indicators','id'), COALESCE((SELECT MAX(id) FROM indicators), 0) + 1, false)");
                DB::statement("SELECT setval(pg_get_serial_sequence('criterias','id'), COALESCE((SELECT MAX(id) FROM criterias), 0) + 1, false)");
                DB::statement("SELECT setval(pg_get_serial_sequence('variables','id'), COALESCE((SELECT MAX(id) FROM variables), 0) + 1, false)");
                DB::statement("SELECT setval(pg_get_serial_sequence('formulas','id'), COALESCE((SELECT MAX(id) FROM formulas), 0) + 1, false)");
                DB::statement("SELECT setval(pg_get_serial_sequence('checklist_items','id'), COALESCE((SELECT MAX(id) FROM checklist_items), 0) + 1, false)");
            } catch (\Throwable $e) {
                // best-effort; continue even if statements fail
                report($e);
            }

            foreach ($indicators as $indicator) {
                // 1) Duplicate indicator
                $copy = Indicator::create([
                    'name'         => $indicator->name,
                    'year'         => $targetYear,
                    'code'         => $indicator->code,
                    'description'  => $indicator->description,
                    'condition'    => $indicator->condition,
                    'annotation'   => $indicator->annotation,
                    'deadline'     => optional($indicator->deadline)->format('Y-m-d'),
                    'status'       => 0,
                    'comment'      => $indicator->comment,
                    'score_acc'    => 0,
                    'max_score'    => $indicator->max_score,
                    'type'         => $indicator->type,
                    'categorie_id' => $indicator->categorie_id,
                ]);

                // 2) Criterias
                foreach ($indicator->criterias as $c) {
                    $criteriaCopy = $copy->criterias()->create([
                        'name'        => $c->name,
                        'description' => $c->description,
                        'sequence'    => $c->sequence,
                        'required_evidence_total' => $c->required_evidence_total,
                        // status/report intentionally not copied to reset workflow content
                    ]);
                    foreach ($c->evidenceRequirements as $req) {
                        $criteriaCopy->evidenceRequirements()->create([
                            'name' => $req->name,
                            'sequence' => $req->sequence,
                        ]);
                    }
                }

                // 3) Variables (keep a map by label_name)
                $variablesMap = [];
                foreach ($indicator->variables as $v) {
                    $nv = $copy->variables()->create([
                        'variable_name' => $v->variable_name,
                        'label_name'    => $v->label_name,
                        'type'          => $v->type,
                        'value'         => $v->value,
                    ]);
                    $variablesMap[$v->label_name] = $nv->id;
                }

                // 4) Formulas (relink variables by label_name)
                foreach ($indicator->formulas as $f) {
                    $nf = $copy->formulas()->create([
                        'condition' => $f->condition,
                    ]);
                    foreach ($f->variables as $fv) {
                        $label = $fv->label_name;
                        if ($label && isset($variablesMap[$label])) {
                            $nf->variables()->attach($variablesMap[$label]);
                        }
                    }
                }

                // 5) Checklist
                foreach ($indicator->checklistItems as $cl) {
                    $copy->checklistItems()->create([
                        'required_items' => $cl->required_items,
                        'score'          => $cl->score,
                        'description'    => $cl->description,
                    ]);
                }
            }
        });

        return back()->with('success', 'Indicators duplicated successfully!');
    }
}
