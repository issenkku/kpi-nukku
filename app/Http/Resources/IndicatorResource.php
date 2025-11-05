<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IndicatorResource extends JsonResource
{
    public function toArray($request)
    {

        $standard = $this->whenLoaded('category', function () {
            return $this->category ? $this->category->standard : null;
        });

        // derive flat users & departments from assignments (collectors)
        $users = $this->whenLoaded('assignments', function () {
            return $this->assignments
                ->map(fn($a) => $a->user)
                ->filter()
                ->unique('id')
                ->values();
        });

        $departments = $users
            ? $users->map(fn($u) => $u->department)->filter()->unique('id')->values()
            : collect();

        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'year'       => $this->year,
            'code'       => $this->code,
            'type'       => $this->type,
            'description' => $this->description,
            'condition'  => $this->condition,
            'annotation' => $this->annotation,
            'deadline'   => optional($this->deadline)->format('Y-m-d'),
            'status'     => $this->status,
            'comment'    => $this->comment,
            'score_acc'  => $this->score_acc,
            'max_score'  => $this->max_score,

            // relations
            'category'       => new CategoryResource($this->whenLoaded('category')),
            'standard'       => new StandardResource($standard),
            'criterias'      => CriteriaResource::collection($this->whenLoaded('criterias')),
            'variable_formula' => [
                'variables'      => VariableResource::collection($this->whenLoaded('variables')),
                'formulas'       => FormulaResource::collection($this->whenLoaded('formulas')),
            ],
            'checklistItems' => ChecklistItemResource::collection($this->whenLoaded('checklistItems')),
            'assignments'    => AssignmentResource::collection($this->whenLoaded('assignments')),
            'departments' => DepartmentResource::collection(
                $this->whenLoaded('assignments', function () {
                    return $this->assignments
                        ->map(fn($a) => $a->user?->department)
                        ->filter()
                        ->unique('id')
                        ->values();
                })
            ),
            'evidences'      => EvidenceResource::collection($this->whenLoaded('evidences')),

        ];
    }
}
