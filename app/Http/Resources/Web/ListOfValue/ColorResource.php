<?php

namespace App\Http\Resources\Web\ListOfValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                       => $this->id,
            'primary_color'            => $this->add_on['primary'] ?? '',
            'secondary_color'          => $this->add_on['secondary'] ?? '',
            'tertiary_color'           => $this->add_on['tertiary'] ?? '',
            'primary_second_color'     => $this->add_on['primary_second'] ?? '',
            'secondary_second_color'   => $this->add_on['secondary_second'] ?? '',
            'tertiary_second_color'    => $this->add_on['tertiary_second'] ?? '',
            'primary_third_color'      => $this->add_on['primary_third'] ?? '',
            'secondary_third_color'    => $this->add_on['secondary_third'] ?? '',
            'tertiary_third_color'     => $this->add_on['tertiary_third'] ?? '',
        ];
    }
}
