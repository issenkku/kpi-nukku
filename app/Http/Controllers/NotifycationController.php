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
            $indicator = Indicator::with(['assignments.collectorUser'])->findOrFail($id);

            foreach ($indicator->assignments as $assignment) {
                if ($assignment->collectorUser) {
                    try {
                        $assignment->collectorUser->notify(
                            new \App\Notifications\IndicatorAssignedNotification($indicator)
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

