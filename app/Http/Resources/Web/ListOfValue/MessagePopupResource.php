<?php

namespace App\Http\Resources\Web\ListOfValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessagePopupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'message'              => [
                'en' => $this->description['en'] ?? null,
                'km' => $this->description['km'] ?? null,
            ],
            'image_url'   => $this->image ? $this->image_url : asset('images/no.jpg'),
            'image'       => $this->image,
        ];
    }
}
