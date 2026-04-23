<?php

namespace App\Http\Resources\Web\ListOfValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'page' => $this->page,
            'content' => [
                'dataDetail' => collect($this->content['dataDetail'] ?? [])
                    ->sortBy('ordering')
                    ->map(function ($item) {
                        return [
                            'description_en' => $item['description_en'] ?? null,
                            'description_km' => $item['description_km'] ?? null,
                            'ordering' => $item['ordering'] ?? null,
                            'image' => $item['image'] ?? null,
                            'image_url' => $this->resolveAssetUrl($item['image'] ?? null),
                        ];
                    })
                    ->values()
                    ->toArray(),
                'imageSlides' => collect($this->content['imageSlides'] ?? [])
                    ->sortBy('ordering')
                    ->map(function ($item) {
                        return [
                            'ordering' => $item['ordering'] ?? null,
                            'image' => $item['image'] ?? null,
                            'image_url' => $this->resolveAssetUrl($item['image'] ?? null),
                        ];
                    })
                    ->values()
                    ->toArray(),
            ],
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

        return asset('storage/list-of-value/' . $file);
    }
}
