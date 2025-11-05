<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VariableResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'variable_name' => $this->variable_name,
            'label_name'    => $this->label_name,
            'type'          => $this->type,
            'value'         => $this->value,
            'indicator_id'  => $this->indicator_id,
        ];
    }
}
