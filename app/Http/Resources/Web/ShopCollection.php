<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ShopCollection extends ResourceCollection
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
                    'id'                => $item?->id,
                    'title'             => $item?->title,
                    'phone'             => $item?->phone,
                    'telegram'          => $item?->telegram,
                    'image'             => $item?->image_url,
                    'status'            => $item?->status,
                    'count_branches'    => $item?->branches_count,
                    'branches'          => $item?->branches?->map(function($branch){
                        return [
                            'id'        => $branch->id,
                            'title'     => $branch->title,
                            'image'     => $branch->image_url,
                            'location'  => $branch->location,
                            'phone'     => $branch->phone,
                            'telegram'  => $branch->telegram,
                            'opened_hour'  => $branch->opening_hour,
                            'closed_hour'  => $branch->close_hour,
                            'image'     => $branch->image_url,
                                'cover'     => $branch->cover_url,
                            'status'    => $branch?->status,
                        ];
                    }),
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
