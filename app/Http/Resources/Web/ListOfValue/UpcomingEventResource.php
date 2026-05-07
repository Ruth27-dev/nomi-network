<?php

namespace App\Http\Resources\Web\ListOfValue;

use App\Models\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UpcomingEventResource extends JsonResource
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
                        'date' => $item['date'] ?? null,
                        'location_en' => $item['location_en'] ?? null,
                        'location_km' => $item['location_km'] ?? null,
                        'description_en' => $item['description_en'] ?? null,
                        'description_km' => $item['description_km'] ?? null,
                        'is_upcoming_event' => (bool) ($item['is_upcoming_event'] ?? false),
                        'ordering' => $item['ordering'] ?? null,
                        'image' => $item['image'] ?? null,
                        'image_url' => $this->resolveAssetUrl($item['image'] ?? null),
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
