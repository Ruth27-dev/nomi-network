<?php

namespace App\Http\Resources\Web\ListOfValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OurTeamResource extends JsonResource
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
            'content' => $this->short_detail,
            'dataDetail' => collect($this->content['dataDetail'] ?? [])
                ->sortBy('ordering')
                ->map(function ($item) {
                    return [
                        'profile' => $item['profile'] ?? null,
                        'profile_url' => isset($item['profile'])
                            ? $item['profile']
                            : asset('images/no.jpg'),
                        'ordering' => $item['ordering'] ?? null,
                        'name_en' => $item['name_en'] ?? null,
                        'name_km' => $item['name_km'] ?? null,
                        'position_en' => $item['position_en'] ?? null,
                        'position_km' => $item['position_km'] ?? null,
                        'description_en' => $item['description_en'] ?? null,
                        'description_km' => $item['description_km'] ?? null,
                    ];
                })
                ->values()
                ->toArray(),
        ];
    }
}