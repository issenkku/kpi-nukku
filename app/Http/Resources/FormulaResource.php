<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FormulaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'condition'    => $this->condition,
            'indicator_id' => $this->indicator_id,
            // If you ever eager-load many-to-many variables via pivot table:
            'variables'    => VariableResource::collection($this->whenLoaded('variables')),
        ];
    }
}
