<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'max_score'  => $this->max_score,
            'standard'   => new StandardResource($this->whenLoaded('standard')),
            'standard_id' => $this->standard_id,
        ];
    }
}
