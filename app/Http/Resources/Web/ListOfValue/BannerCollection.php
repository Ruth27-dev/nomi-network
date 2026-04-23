<?php

namespace App\Http\Resources\Web\ListOfValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BannerCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'banner_page' => $item->banner_page,
                'ordering' => $item->ordering,
                'url' => $item->url,
                'image' => $item->image,
                'image_url' => $item->image_url,
            ];
        })->toArray();
    }
}
