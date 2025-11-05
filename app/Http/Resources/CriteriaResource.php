<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CriteriaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'sequence'    => $this->sequence,
            'status'      => $this->status,
            'indicator_id' => $this->indicator_id,
            // Include per-criteria evidences if you want them here too:
            'evidences'   => EvidenceResource::collection($this->whenLoaded('evidences')),
        ];
    }
}
