<?php

namespace App\Http\Resources\Web\ListOfValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SocialMediaCollection extends ResourceCollection
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
                'id' => $item->id,
                'title' => $item->title,
                'banner_page' => $item->banner_page,
                'image' => $item->image,
                'image_url' => $item->image
                    ? $item->image_url
                    : asset('images/no.jpg'),
                'ordering' => $item->ordering,
                'url' => $item->url,
            ];
        })->toArray();
    }
}
