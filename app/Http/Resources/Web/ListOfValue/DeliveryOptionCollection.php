<?php

namespace App\Http\Resources\Web\ListOfValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DeliveryOptionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($item) {
            return [
                'id'            => $item->id,
                'title'         => $item->title,
                'description'   => $item->description,
                'ordering'      => $item->sequence,
                'price'         => $item->add_on['price'],
            ];
        })->toArray();
    }
}