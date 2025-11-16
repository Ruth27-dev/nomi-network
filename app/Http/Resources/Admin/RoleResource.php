<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'display_name_en'   => $this->display_name['en'],
            'display_name_km'   => $this->display_name['km'],
            'guard_name'        => $this->guard_name,
            'status'            => $this->status,
        ];
    }
}
