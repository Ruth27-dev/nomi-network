<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'gender'            => $this->gender,
            'phone'             => $this->phone,
            'type'              => $this->type,
            'profile'           => $this->profile,
            'profile_url'       => $this->profile_url,
            'address'           => $this->address,
            'is_online'         => $this->is_online,
            'commission'        => $this->commission,
            'total_balance'     => $this->total_balance,
        ];
    }
}
