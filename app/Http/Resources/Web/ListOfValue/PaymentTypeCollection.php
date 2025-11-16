<?php

namespace App\Http\Resources\Web\ListOfValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PaymentTypeCollection extends ResourceCollection
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
                'branch_id'     => $item->branch_id,
                'title'         => $item->title,
                'description'   => $item->description,
                'ordering'      => $item->sequence,
                'image'         => $item->image ? $item->image_url : null,
                'branch'        => $item->branch ? [
                    'id'    => $item->branch_id,
                    'title' => $item->branch->title,
                ] : null,
            ];
        })->toArray();
    }
}