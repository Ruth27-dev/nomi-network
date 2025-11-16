<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data'  => $this->collection->map(function($item){
                return [
                    'id'                => $item->id,
                    'branch_id'         => $item->branch_id,
                    'name'              => $item->name,
                    'email'             => $item->email,
                    'phone'             => $item->phone,
                    'gender_id'         => $item->gender_id,
                    'role'              => $item->role,
                    'default_language'  => $item->default_language,
                    'profile'           => $item?->profile ? $item->profile_url : null,
                    'address'           => $item->address,
                    'status'            => $item->status,
                ];
            }),
            'paginate' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage(),
                'next_page_url' => $this->nextPageUrl(),
                'prev_page_url' => $this->previousPageUrl(),
            ],
        ];
    }
}
