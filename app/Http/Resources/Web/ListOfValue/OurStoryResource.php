<?php

namespace App\Http\Resources\Web\ListOfValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OurStoryResource extends JsonResource
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
            'content' => [
                'en'=>$this->content['en'],
                'km'=>$this->content['km']
            ],
            'dataDetail' => collect($this->content['dataDetail'] ?? [])
                ->sortBy('ordering')
                ->map(function ($item) {
                    return [
                        'icon' => $item['icon'] ?? null,
                        'icon_url' => isset($item['icon'])
                            ? $item['icon']
                            : asset('images/no.jpg'),
                        'ordering' => $item['ordering'] ?? null,
                        'title_en' => $item['title_en'] ?? null,
                        'title_km' => $item['title_km'] ?? null,
                        'description_en' => $item['description_en'] ?? null,
                        'description_km' => $item['description_km'] ?? null,
                    ];
                })
                ->values()
                ->toArray(),
        ];
    }
}