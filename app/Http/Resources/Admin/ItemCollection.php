<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ItemCollection extends ResourceCollection
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
                    'code'              => $item->code,
                    'title'             => $item->title,
                    'description'       => $item->description,
                    'image'             => $item?->image ? $item->image_url : null,
                    'sequence'          => $item->sequence,
                    'status'            => $item->status,
                    'is_sellable'       => boolval($item->is_sellable),
                    'is_consumable'     => boolval($item->is_consumable),
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
