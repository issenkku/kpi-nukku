<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChecklistItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'required_items' => $this->whenNotNull($this->required_items), // JSON
            'score'        => $this->score,
            'sequence'     => $this->sequence,
            'indicator_id' => $this->indicator_id,
        ];
    }
}
