<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    public function toArray($request)
    {
        
        return [
            'indicator_id' => $this->indicator_id,
            'collector'    => $this->collector, // user id
            'user'         => new UserResource($this->whenLoaded('user')), // collector user

        ];
    }
}
