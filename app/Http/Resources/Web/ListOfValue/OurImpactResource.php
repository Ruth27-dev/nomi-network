<?php

namespace App\Http\Resources\Web\ListOfValue;

use App\Models\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OurImpactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'page' => $this->page,
            'title' => $this->title,
            'short_detail' => $this->short_detail,
            'dataDetail' => collect($this->content['dataDetail'] ?? [])
                ->sortBy('ordering')
                ->map(function ($item) {
                    $imagePaths = $item['images'] ?? ($item['image'] ? [$item['image']] : []);
                    return [
                        'title_en'       => $item['title_en'] ?? null,
                        'title_km'       => $item['title_km'] ?? null,
                        'description_en' => $item['description_en'] ?? null,
                        'description_km' => $item['description_km'] ?? null,
                        'ordering'       => $item['ordering'] ?? null,
                        'image'          => $item['image'] ?? $imagePaths[0] ?? null,
                        'image_url'      => $this->resolveAssetUrl($imagePaths[0] ?? $item['image'] ?? null),
                        'images'         => array_values(array_filter(array_map(
                            fn($img) => $this->resolveAssetUrl($img),
                            $imagePaths
                        ))),
                        'icon'     => $item['icon'] ?? null,
                        'icon_url' => $this->resolveAssetUrl($item['icon'] ?? null),
                    ];
                })
                ->values()
                ->toArray(),
        ];
    }

    private function resolveAssetUrl(?string $file): ?string
    {
        if (!$file) {
            return null;
        }

        if (filter_var($file, FILTER_VALIDATE_URL)) {
            return $file;
        }

        return UploadFile::resolvePublicUrl($file, 'list-of-value');
    }
}
