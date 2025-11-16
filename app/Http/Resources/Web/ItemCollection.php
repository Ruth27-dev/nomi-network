<?php

namespace App\Http\Resources\Web;

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
                    'id'                => $item?->id,
                    'code'              => $item?->code,
                    'title'             => $item?->title,
                    'description'       => $item?->description,
                    'image_url'         => $item?->image_url,
                    'min_price'         => $item?->itemVariates->pluck('product.price')->filter()->min(),
                    'max_price'         => $item?->itemVariates->pluck('product.price')->filter()->max(),
                    'branch'            => $item?->branch ? $item->branch->title : null,
                    'shop'              => $item?->branch?->shop ? $item->branch->shop->title : null,
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
