<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'employee_id'   => $this->employee_id,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'status'        => (bool) $this->status,
            'department_id' => $this->department_id,
            // 'department'    => new DepartmentResource($this->whenLoaded('department')),
        ];
    }
}
