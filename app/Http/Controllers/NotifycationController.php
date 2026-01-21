<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotifycationController extends Controller
{
    public function notifyCollectors(Request $request, $id)
    {
        try {
            $indicator = Indicator::with([
                'assignments.collectorUser',
                'criterias.evidenceRequirements',
                'criterias.evidences',
            ])->findOrFail($id);

            $missingRequirements = collect();
            foreach ($indicator->criterias as $criteria) {
                $requirements = $criteria->evidenceRequirements->sortBy('sequence');
                if ($requirements->isEmpty()) {
                    continue;
                }

                $uploaded = $criteria->evidences ?? collect();
                $uploadedByReq = $uploaded
                    ->pluck('criteria_evidence_requirement_id')
                    ->filter()
                    ->unique()
                    ->flip();

                foreach ($requirements as $req) {
                    if (! $uploadedByReq->has($req->id)) {
                        $name = trim((string) ($req->name ?? ''));
                        if ($name !== '') {
                            $missingRequirements->push($name);
                        }
                    }
                }
            }

            $missingRequirements = $missingRequirements->unique()->values()->all();

            foreach ($indicator->assignments as $assignment) {
                if ($assignment->collectorUser) {
                    try {
                        $assignment->collectorUser->notify(
                            new \App\Notifications\IndicatorAssignedNotification($indicator, $missingRequirements)
                        );
                    } catch (Exception $e) {
                        Log::error('Failed to notify user ID: ' . $assignment->collectorUser->id, [
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'ok',
                    'message' => 'Notifications dispatched to assignees.',
                    'indicator_id' => (int) $id,
                ]);
            }
            return back()->with('success', 'Notifications dispatched to assignees.');
        } catch (Exception $e) {
            Log::error('Notify assignees failed for Indicator ID: ' . $id, [
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Failed to notify assignees: ' . $e->getMessage());
        }
    }
}
