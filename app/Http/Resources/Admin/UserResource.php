<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'branch_id'         => $this->branch_id,
            'name'              => $this->name,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'gender_id'         => $this->gender_id,
            'role'              => $this->role,
            'default_language'  => $this->default_language,
            'profile'           => $this?->profile ? $this->profile_url : null,
            'address'           => $this->address,
            'status'            => $this->status,
        ];
    }
}
