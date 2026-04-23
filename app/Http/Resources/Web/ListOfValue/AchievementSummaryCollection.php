<?php

namespace App\Http\Resources\Web\ListOfValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AchievementSummaryCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'number' => $item->code,
                'title' => $item->title,
                'description' => $item->description,
                'sequence' => $item->sequence,
                'image' => $item->image,
                'image_url' => $item->image_url,
            ];
        })->toArray();
    }
}
