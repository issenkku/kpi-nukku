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
            'required_evidence_total' => $this->required_evidence_total,
            'evidence_requirements' => $this->whenLoaded('evidenceRequirements', function () {
                return $this->evidenceRequirements->map(function ($req) {
                    return [
                        'id' => $req->id,
                        'name' => $req->name,
                        'sequence' => $req->sequence,
                    ];
                })->values();
            }),
            // Include per-criteria evidences if you want them here too:
            'evidences'   => EvidenceResource::collection($this->whenLoaded('evidences')),
        ];
    }
}
