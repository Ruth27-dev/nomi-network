<?php

namespace App\Http\Resources\Web\ListOfValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'name'              => [
                'en' => $this->add_on['name']['en'] ?? null,
                'km' => $this->add_on['name']['km'] ?? null,
            ],
            'address'           => [
                'en' => $this->add_on['address']['en'] ?? null,
                'km' => $this->add_on['address']['km'] ?? null,
            ],
            'phone'             => $this->add_on['phone'] ?? null,
            'logo'           => $this->image,
            'logo_url'       => $this->image_url,
        ];
    }
}
