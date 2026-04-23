<?php

namespace App\Http\Resources\Web\ListOfValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SocialMediaCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'image' => $item->image,
                'image_url' => $item->image_url,
                'ordering' => $item->ordering,
                'url' => $item->url,
            ];
        })->toArray();
    }
}
