<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DriverCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data'  => $this->collection->map(function ($item) {
                return [
                    'id'                => $item?->id,
                    'branch_id'         => $item?->branch_id,
                    'name'              => [
                        'en' => $item?->name,
                        'km' => $item?->name_km
                    ],
                    'gender'             => $item?->gender,
                    'date_of_birth'      => $item?->date_of_birth ? dateTimeFormat($item?->date_of_birth) : null,
                    'email'              => $item?->email,
                    'phone'              => [
                        'first' => $item?->phone,
                        'second' => $item?->second_phone
                    ],
                    'facebook'            => $item?->facebook,
                    'type'                => $item?->type,
                    'default_language'    => $item?->default_language,
                    'profile'             => $item?->profile,
                    'address'             => $item?->address,
                    'is_online'           => $item?->is_online,
                    'commission'          => $item?->commission,
                    'total_balance'       => $item?->total_balance,
                    'status'              => $item?->status,
                    'profile_url'         => $item?->profile_url,
                ];
            }),
            'total' => $this->collection->count(),
        ];
    }
}
