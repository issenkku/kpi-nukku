<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Indicator;
use App\Models\Criteria;
use App\Models\Variable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardKpiUserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;
        $deptId = $user->department_id;

        $query = Indicator::with([
            'category.standard',
            'assignments.user.department',
            'criterias',
        ]);

        // ✅ ผู้ใช้ทั่วไป (role: user) เห็นเฉพาะที่ถูกมอบหมายให้ตนเอง หรืออยู่ในแผนกเดียวกัน
        // ✅ บทบาทอื่นๆ (admin/super_admin/ฯลฯ) เห็นทั้งหมด (ไม่กรอง)
        if ($user->hasRole('user')) {
            $query->where(function ($q) use ($userId, $deptId) {
                $q->whereHas('assignments', fn ($qa) => $qa->where('collector', $userId))
                    ->orWhereHas('assignments.user', fn ($qu) => $qu->where('department_id', $deptId));
            });
        }

        $indicators = $query
            ->orderByRaw("
            CASE LEFT(code, 3)
                WHEN 'NCS' THEN 1
                WHEN 'NCO' THEN 2
                WHEN 'NCP' THEN 3
                ELSE 4
            END
        ")
            ->orderByRaw("
            CASE 
                WHEN split_part(code, '-', 2) ~ '^[0-9]+$' 
                THEN CAST(split_part(code, '-', 2) AS INTEGER)
                ELSE 999999
            END
        ")
            ->get()
            ->map(function ($i) use ($userId) {
                $data = $this->serializeIndicatorForList($i);

                // ✅ Check if current user is directly assigned
                $data['is_assigned'] = $i->assignments->contains(fn ($a) => $a->collector == $userId);

                return $data;
            });

        // dd($indicators);
        return view('kpi_dashboard_assigned.app', compact('indicators'));
    }

    public function show(Request $request, $id)
    {
        $indicator = Indicator::with([
            'category.standard',
            'assignments.collectorUser.department',
            'criterias' => function ($query) {
                $query->orderBy('sequence', 'asc');
            },
            'criterias.evidenceRequirements',
            'criterias.evidences.user.department',
            'criterias.evidences.requirement',
            'variables',
            'formulas.variables',
            'checklistItems',
        ])->findOrFail($id);

        $criteria = $indicator->criterias->first(); // อาจเป็น null ได้
        $criteria_id = optional($criteria)->id;

        $user = Auth::user();

        // Optional UI hint from query string (may be 'assigned' or 'is_assigned')
        // Do NOT use this for authorization/branching
        $assignedHint = $request->has('is_assigned')
            ? $request->boolean('is_assigned')
            : $request->boolean('assigned');

        // ตรวจสอบว่าผู้ใช้ถูกมอบหมายโดยตรงหรือไม่ (authoritative)
        $isAssigned = $indicator->assignments->contains(function ($a) use ($user) {
            return (int) ($a->collector ?? 0) === (int) $user->id;
        });

        // Branch view ตามสถานะการมอบหมายจริง
        if ($isAssigned) {
            return view('kpi_dashboard_assigned.show', compact('indicator', 'criteria_id'));
        }

        return view('kpi_dashboard_assigned.show_readOnly', compact('indicator', 'criteria_id'));
    }

    public function saveVariables(Request $request, $id)
    {
        $indicator = Indicator::findOrFail($id);
        $previousStatus = (int) ($indicator->status ?? 0);

        if ($request->has('variables')) {
            foreach ($request->variables as $varId => $value) {
                $normalizedValue = $this->normalizeVariableValue($value);
                Variable::updateOrCreate(
                    ['id' => $varId, 'indicator_id' => $indicator->id],
                    ['value' => $normalizedValue]
                );
            }
        }

        if ($request->has('status')) {
            $indicator->status = $request->status;
        }

        if ($request->has('criterias')) {
            foreach ($request->criterias as $criteriaId => $criteriaData) {
                if (array_key_exists('evidence_comment', $criteriaData)) {
                    Criteria::where('id', $criteriaId)->update([
                        'evidence_comment' => $criteriaData['evidence_comment'],
                    ]);
                }
            }
        }

        $indicator->save();

        // Notify QA when user submits final (status=2)
        try {
            if ((int) ($indicator->status ?? 0) === 2 && $previousStatus !== 2) {
                $qaUsers = \App\Models\User::role(['qa_admin','super_admin','system_admin'])->get();
                foreach ($qaUsers as $qa) {
                    $qa->notify(new \App\Notifications\IndicatorFinalSubmittedNotification($indicator, optional(\Illuminate\Support\Facades\Auth::user())->name));
                }
            }
        } catch (\Throwable $e) {
            // Silently ignore email errors
        }

        return redirect()->route('dashboardkpi.user.show', $indicator->id)
            ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');

    }

    // User requests correction/unlock -> notify QA
    public function requestCorrection(Request $request, $id)
    {
        $indicator = Indicator::findOrFail($id);
        $note = (string) $request->input('note', '');

        try {
            $qaUsers = \App\Models\User::role(['qa_admin','super_admin','system_admin'])->get();
            $by = optional(\Illuminate\Support\Facades\Auth::user())->name;
            foreach ($qaUsers as $qa) {
                $qa->notify(new \App\Notifications\IndicatorCorrectionRequestedNotification($indicator, $by, $note));
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return redirect()->route('dashboardkpi.user.show', $indicator->id)
            ->with('success', 'ส่งคำร้องขอแก้ไขไปยังเจ้าหน้าแล้ว');
    }

    /**
     * Normalize incoming variable values so empty strings don't violate numeric columns.
     */
    private function normalizeVariableValue($value): ?float
    {
        if (is_null($value)) {
            return null;
        }

        if (is_string($value)) {
            $value = trim($value);
        }

        if ($value === '') {
            return null;
        }

        return is_numeric($value) ? (float) $value : null;
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
        $hasPending = $i->criterias->contains(fn ($c) => (int) ($c->status ?? -1) === 0);
        $hasIncomplete = $i->criterias->contains(fn ($c) => (int) ($c->status ?? -1) === 2);
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
}
