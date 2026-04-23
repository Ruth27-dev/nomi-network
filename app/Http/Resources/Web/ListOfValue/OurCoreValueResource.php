<?php

namespace App\Http\Resources\Web\ListOfValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OurCoreValueResource extends JsonResource
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
                    return [
                        'title_en' => $item['title_en'] ?? null,
                        'title_km' => $item['title_km'] ?? null,
                        'description_en' => $item['description_en'] ?? null,
                        'description_km' => $item['description_km'] ?? null,
                        'ordering' => $item['ordering'] ?? null,
                        'icon' => $item['icon'] ?? null,
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

        return asset('storage/list-of-value/' . $file);
    }
}
