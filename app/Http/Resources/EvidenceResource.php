<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EvidenceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'path'       => $this->path,
            'type'       => $this->type,
            'detail'     => $this->detail,
            'status'     => (bool) $this->status,
            'criteria_id' => $this->criteria_id,
            'user'       => new UserResource($this->whenLoaded('user')),
            'user_id'    => $this->user_id,
        ];
    }
}
