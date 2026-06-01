<?php

namespace App\Http\Resources\Web\ListOfValue;

use App\Models\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MissionVisionCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($item) {
            $imagePaths = data_get($item->add_on, 'images', $item->image ? [$item->image] : []);
            return [
                'id'         => $item->id,
                'title'      => $item->title,
                'description'=> $item->description,
                'type'       => data_get($item, 'add_on.type'),
                'sequence'   => $item->sequence,
                'image'      => $item->image,
                'image_url'  => $item->image_url,
                'images'     => array_values(array_filter(array_map(
                    fn($img) => UploadFile::resolvePublicUrl($img, 'list-of-value'),
                    $imagePaths
                ))),
            ];
        })->toArray();
    }
}
