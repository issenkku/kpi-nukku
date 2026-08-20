<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use App\Notifications\IndicatorAssignedNotification;
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
                            new IndicatorAssignedNotification($indicator, $missingRequirements)
                        );
                    } catch (\Throwable $e) {
                        Log::error('Failed to notify user ID: '.$assignment->collectorUser->id, [
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
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Notify assignees failed for Indicator ID: '.$id, [
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unable to send notifications.',
                ], 500);
            }

            return back()->with('error', 'Unable to send notifications.');
        }
    }
}
