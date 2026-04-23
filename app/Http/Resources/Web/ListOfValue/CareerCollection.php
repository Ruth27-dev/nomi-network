<?php

namespace App\Http\Resources\Web\ListOfValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CareerCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'position' => $item->title,
                'location' => $item->description,
                'close_date' => data_get($item, 'add_on.close_date'),
                'sequence' => $item->sequence,
            ];
        })->toArray();
    }
}
