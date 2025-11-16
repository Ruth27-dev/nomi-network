<?php

namespace App\Http\Resources\Web\ListOfValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WhyChooseUsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'page' => $this->page,
            'title' => $this->title,
            'subtitle' => $this->short_detail,
            'dataDetail' => collect($this->content['dataDetail'] ?? [])
                ->sortBy('ordering')
                ->map(function ($item) {
                    return [
                        'icon' => $item['icon'] ?? null,
                        'icon_url' => isset($item['icon'])
                            ? $item['icon']
                            : asset('images/no.jpg'),
                        'ordering' => $item['ordering'] ?? null,
                        'title' => [
                            'en' => $item['title_en'] ?? null,
                            'km' => $item['title_km'] ?? null,
                        ],
                        'description_en' => [
                            'en' => $item['description_en'] ?? null,
                            'km' => $item['description_km'] ?? null,
                        ]
                    ];
                })
                ->values()
                ->toArray(),
        ];
    }
}