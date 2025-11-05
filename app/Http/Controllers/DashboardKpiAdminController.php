<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Indicator;
use App\Models\Variable;
use App\Models\Criteria;
use App\Models\Evidence;
use Illuminate\Database\Eloquent\Casts\Json;
use Laravel\Pail\ValueObjects\Origin\Console;

class DashboardKpiAdminController extends Controller
{

    public function show(Request $request,$id)
    {
        $indicator = Indicator::with([
            'category.standard',
            'assignments.collectorUser.department',
            'criterias' => function($query) {
                $query->orderBy('sequence', 'asc');
            },
            'criterias.evidences.user.department',
            'variables',
            'formulas.variables',
            'checklistItems',
        ])->findOrFail($id);
        // dd($indicator);
        return view('kpi_dashboard_assigned.vrf_show', compact('indicator'));
    }

    public function saveVariables(Request $request, $id)
    {
        $indicator = Indicator::findOrFail($id);
        $previousStatus = (int) ($indicator->status ?? 0);

        // variables
        if ($request->has('variables')) {
            foreach ($request->variables as $varId => $value) {
                Variable::updateOrCreate(
                    ['id' => $varId, 'indicator_id' => $indicator->id],
                    ['value' => $value]
                );
            }
        }

        // criterias
        if ($request->has('criterias')) {
            foreach ($request->criterias as $criteriaId => $criteriaData) {
                if (isset($criteriaData['status'])) {
                    Criteria::where('id', $criteriaId)->update(['status' => $criteriaData['status']]);
                }
            }
        }

        // evidences
        // if ($request->has('evidences')) {
        //     foreach ($request->evidences as $evidenceId => $evidenceData) {
        //         if (isset($evidenceData['status'])) {
        //             Evidence::where('id', $evidenceId)->update([
        //                 'status' => $evidenceData['status'] === 'true'
        //             ]);
        //         }
        //     }
        // }

        if ($request->has('status')) {
            $indicator->status = $request->status;
        } else {
            $indicator->status = 1; // Default value
        }

        $indicator->save();

        if ($indicator->status == 2 || $request->status == 2) {
            $this->calculateScore($indicator);
        }

        // Notify assignees on status changes handled by QA
        try {
            $newStatus = (int) ($indicator->status ?? 0);
            if ($newStatus !== $previousStatus) {
                $indicator->loadMissing(['assignments.collectorUser']);
                $changedBy = optional(\Illuminate\Support\Facades\Auth::user())->name;
                // 1) Any -> draft (1): notify assignees they can edit again
                if ($newStatus === 1 && $previousStatus !== 1) {
                    foreach ($indicator->assignments as $assignment) {
                        if ($assignment->collectorUser) {
                            $recipient = $assignment->collectorUser;
                            $email = (string) ($recipient->email ?? '');
                            \Illuminate\Support\Facades\Log::info('Notify assignee about status change (*->1)', [
                                'indicator_id' => $indicator->id,
                                'recipient_id' => $recipient->id ?? null,
                                'email' => $email,
                                'prev' => $previousStatus,
                                'new' => $newStatus,
                            ]);
                            if ($email === '') {
                                \Illuminate\Support\Facades\Log::warning('Skip notify: recipient has no email', [
                                    'recipient_id' => $recipient->id ?? null,
                                ]);
                                continue;
                            }
                            $assignment->collectorUser->notify(new \App\Notifications\IndicatorStatusChangedForAssignees($indicator, $newStatus, $previousStatus, $changedBy));
                        }
                    }
                }
                // 2) QA sets to 3 or 4 -> notify assignees
                if (in_array($newStatus, [3, 4], true)) {
                    foreach ($indicator->assignments as $assignment) {
                        if ($assignment->collectorUser) {
                            $recipient = $assignment->collectorUser;
                            $email = (string) ($recipient->email ?? '');
                            \Illuminate\Support\Facades\Log::info('Notify assignee about status change (QA->assignee)', [
                                'indicator_id' => $indicator->id,
                                'recipient_id' => $recipient->id ?? null,
                                'email' => $email,
                                'prev' => $previousStatus,
                                'new' => $newStatus,
                            ]);
                            if ($email === '') {
                                \Illuminate\Support\Facades\Log::warning('Skip notify: recipient has no email', [
                                    'recipient_id' => $recipient->id ?? null,
                                ]);
                                continue;
                            }
                            $assignment->collectorUser->notify(new \App\Notifications\IndicatorStatusChangedForAssignees($indicator, $newStatus, $previousStatus, $changedBy));
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Notify assignees failed', [
                'indicator_id' => $indicator->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }

        // ✅ ส่ง JSON กลับไป
        return redirect()->route('dashboardkpi.admin.show', $indicator->id)
            ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');

        // return response()->json($request->all());
    }

    private function calculateScore($indicator)
    {
        $scoreAcc = 0;

        // Check if indicator has variables (formula-based calculation)
        if ($indicator->variables->isNotEmpty()) {
            $scoreAcc = $this->calculateFormulaScore($indicator);
        }
        // Check if indicator has checklist items (checklist-based calculation)
        elseif ($indicator->checklistItems->isNotEmpty()) {
            $scoreAcc = $this->calculateChecklistScore($indicator);
        }

        // Update the indicator's score_acc
        $indicator->score_acc = $scoreAcc;
        $indicator->save();

        return $scoreAcc;
    }

    private function calculateChecklistScore($indicator)
    {
        // Get all criteria with status = 1 (passed criteria)
        $passedCriteria = $indicator->criterias->where('status', 1);

        // Create array of passed criteria sequences (sorted and normalized to int for comparison)
        $passedCriteriaSequences = $passedCriteria
            ->pluck('sequence')
            ->map(fn($v) => (int) $v)
            ->sort()
            ->values()
            ->toArray();

        Log::info("Passed criteria sequences for indicator {$indicator->id}: " . json_encode($passedCriteriaSequences));

        // Check each checklist item for exact match
        foreach ($indicator->checklistItems as $checklistItem) {
            $requiredItems = $checklistItem->required_items ?? [];

            // Normalize and sort required items for comparison
            $sortedRequiredItems = collect($requiredItems)
                ->map(fn($v) => (int) $v)
                ->sort()
                ->values()
                ->toArray();

            Log::info("Checking checklist item {$checklistItem->id}, required sequences: " . json_encode($sortedRequiredItems));
            Log::info("Comparing with passed sequences: " . json_encode($passedCriteriaSequences));

            // Check if the passed criteria sequences exactly match the required items
            if ($passedCriteriaSequences === $sortedRequiredItems) {
                $itemScore = $checklistItem->score ?? 0;

                Log::info("Exact match found! Using score {$itemScore} from checklist item {$checklistItem->id}");

                return $itemScore; // Return immediately when exact match is found
            } else {
                Log::info("No exact match for checklist item {$checklistItem->id}");
            }
        }

        // If no exact match found, return 0
        Log::info("No checklist item matched the passed criteria sequences for indicator {$indicator->id}");

        return 0;
    }

    private function calculateFormulaScore($indicator)
    {
        // Refresh to ensure latest values
        $indicator->refresh();

        $formulas = $indicator->formulas;
        $anyOutputUpdated = false;
        $lastOutcome = 0.0;

        foreach ($formulas as $formula) {
            $raw = (string) ($formula->condition ?? '');

            // 1) Find all tokens that look like variable names (letters, digits, underscore)
            preg_match_all('/[A-Za-z_][A-Za-z0-9_]*/', $raw, $matches);
            $tokens = array_unique($matches[0] ?? []);

            // 2) Build a map of variable name => numeric value from indicator variables
            $varMap = [];
            foreach ($indicator->variables as $v) {
                // Only allow known types to be injected
                if (in_array($v->type, ['input', 'defined', 'output'])) {
                    $varMap[$v->variable_name] = is_null($v->value) ? 0 : (float) $v->value;
                }
            }

            // 3) Replace tokens that exist in varMap using word-boundary safe replacement
            $condition = $raw;
            foreach ($tokens as $name) {
                if (array_key_exists($name, $varMap)) {
                    $condition = preg_replace('/\b' . preg_quote($name, '/') . '\b/', (string) $varMap[$name], $condition);
                }
            }

            try {
                Log::info("Evaluating formula for indicator {$indicator->id}: {$condition}");
                $outcome = (float) $this->evaluateFormula($condition);
                if (!is_finite($outcome)) {
                    throw new \RuntimeException('Non-finite outcome (NaN/INF)');
                }
                $lastOutcome = $outcome;

                // 4) If this formula has an output variable, store the outcome there
                $outputVariable = $formula->variables->firstWhere('type', 'output');
                if ($outputVariable) {
                    $outputVariable->value = $outcome;
                    $outputVariable->save();
                    $anyOutputUpdated = true;
                    Log::info("Updated output variable {$outputVariable->variable_name} with value: {$outcome}");
                }
            } catch (\Throwable $e) {
                Log::error("Formula calculation error for indicator {$indicator->id}, formula: '{$raw}', compiled: '{$condition}', error: " . $e->getMessage());
                // Continue with next formula
            }
        }

        // 5) If there are output variables for this indicator, score_acc is the sum of them
        // Otherwise, if we evaluated at least one formula without outputs, use the last outcome
        $indicator->refresh();
        $sumOutputs = (float) $indicator->variables()->where('type', 'output')->sum('value');
        if ($sumOutputs > 0 || $anyOutputUpdated) {
            Log::info("Total calculated score from output variables for indicator {$indicator->id}: {$sumOutputs}");
            return $sumOutputs;
        }

        // Fallback: no output variables exist; use last outcome (or 0 if none)
        Log::info("No output variables found; using last outcome for indicator {$indicator->id}: {$lastOutcome}");
        return $lastOutcome;
    }

    private function evaluateFormula($expression)
    {
        // Remove any whitespace around the expression
        $expression = trim($expression);

        // Handle IF function: IF(condition, true_value, false_value)
        $expression = $this->handleIfFunction($expression);

        // Log the converted expression for debugging
        Log::info("Converted expression: {$expression}");

        // Enhanced security check - allow numbers, operators, parentheses, decimal points, and comparison operators
        // Also allow question mark and colon for ternary operator
        if (!preg_match('/^[0-9+\-*\/\.\(\)\s<>=!&|?:]+$/', $expression)) {
            throw new \Exception("Invalid formula expression: contains unauthorized characters. Expression: {$expression}");
        }

        // Evaluate the mathematical expression safely
        try {
            // Use eval() with extreme caution - only after validation
            $result = eval("return $expression;");

            if ($result === false || is_null($result)) {
                throw new \Exception("Formula evaluation failed");
            }

            return (float) $result;
        } catch (\ParseError $e) {
            throw new \Exception("Formula syntax error: " . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception("Formula execution error: " . $e->getMessage());
        }
    }

    private function handleIfFunction($expression)
    {
        // Convert Excel-like IF(condition, true_value, false_value) into PHP ternary
        // Use a parenthesis-aware parser to avoid infinite loops and support nesting
        $maxIterations = 100; // safety guard
        $result = $expression;

        while (($pos = stripos($result, 'IF(')) !== false && $maxIterations-- > 0) {
            $ifStart = $pos;               // position of 'I' in 'IF('
            $openParen = strpos($result, '(', $ifStart + 2); // find '(' after IF
            if ($openParen === false) {
                break; // malformed IF, stop processing
            }

            // Find the matching closing parenthesis for this IF(
            $depth = 0;
            $closeParen = null;
            $len = strlen($result);
            for ($i = $openParen; $i < $len; $i++) {
                $ch = $result[$i];
                if ($ch === '(') {
                    $depth++;
                } elseif ($ch === ')') {
                    $depth--;
                    if ($depth === 0) {
                        $closeParen = $i;
                        break;
                    }
                }
            }

            if ($closeParen === null) {
                // Unbalanced parentheses; stop to avoid infinite loop
                break;
            }

            // Extract the inside of IF(...)
            $inside = substr($result, $openParen + 1, $closeParen - $openParen - 1);

            // Split into 3 parts by top-level commas (ignore commas in nested parentheses)
            $parts = $this->splitIfArgs($inside);
            if (count($parts) !== 3) {
                // Cannot parse properly; stop to avoid infinite loop
                break;
            }

            // Recursively handle nested IFs within parts
            $condition = trim($this->handleIfFunction($parts[0]));
            $trueValue = trim($this->handleIfFunction($parts[1]));
            $falseValue = trim($this->handleIfFunction($parts[2]));

            // Build PHP ternary expression
            $ternary = "(" . $condition . " ? " . $trueValue . " : " . $falseValue . ")";

            // Replace the whole IF(...) segment with the ternary
            $result = substr($result, 0, $ifStart) . $ternary . substr($result, $closeParen + 1);
        }

        return $result;
    }

    /**
     * Split a string of IF arguments into exactly three parts by top-level commas,
     * ignoring commas inside nested parentheses.
     * Example input: "A > 1, IF(B>2, 3, 4), 0"
     * Output: ["A > 1", " IF(B>2, 3, 4)", " 0"]
     */
    private function splitIfArgs(string $args): array
    {
        $parts = [];
        $start = 0;
        $depth = 0;
        $len = strlen($args);
        for ($i = 0; $i < $len; $i++) {
            $c = $args[$i];
            if ($c === '(') {
                $depth++;
            } elseif ($c === ')') {
                if ($depth > 0) {
                    $depth--;
                }
            } elseif ($c === ',' && $depth === 0) {
                $parts[] = substr($args, $start, $i - $start);
                $start = $i + 1;
            }
        }
        // Add final segment
        $parts[] = substr($args, $start);

        // Return at most 3 parts; extra commas are considered part of the last argument
        if (count($parts) > 3) {
            $parts = [
                $parts[0],
                $parts[1],
                implode(',', array_slice($parts, 2)),
            ];
        }

        return $parts;
    }
}
