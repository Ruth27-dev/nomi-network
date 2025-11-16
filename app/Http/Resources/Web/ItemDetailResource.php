<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this?->id,
            'code'          => $this?->code,
            'title'         => $this?->title,
            'description'   => $this?->description,
            'image_url'     => $this?->image_url,
            'min_price'     => $this?->itemVariates->pluck('product.price')->filter()->min(),
            'max_price'     => $this?->itemVariates->pluck('product.price')->filter()->max(),
            'is_popular'    => $this?->is_popular,
            'unit'          => $this?->unit ? $this->unit->title : null,
            'variates'      => $this?->itemVariates->map(function ($variate) {
                return [
                    'id'            => $variate?->id,
                    'product_id'    => $variate?->product->id,
                    'title'         => $variate?->title,
                    'image_url'     => $variate?->image_url,
                    'is_available'  => $variate?->product->is_available,
                    'is_pre_order'  => false,
                    'description'   => $variate->product->description,
                    'price'         => $variate->product->price,
                    'size'          => $variate->product->size,
                    'unit'          => $this?->unit?->title,
                    'is_note'       => $variate->product->is_note,
                    'cooking_duration' => $variate->product?->group['add_on']['cooking_duration'] ?? null,
                    'note'          => $variate?->product?->is_note ?
                        $variate->product->note :
                        [
                            'en'    => $this->unit && $variate?->product?->size ? $variate?->product?->size . ' ' . $this?->unit?->title['en'] : '',
                            'km'    => $this->unit && $variate?->product?->size ? $variate?->product?->size . ' ' . $this?->unit?->title['km'] : '',
                        ],

                    'branch'    => $this->branch ? [
                        'id'        => $this->branch->id,
                        'title'     => $this->branch->title,
                        'image'     => $this->branch->image_url,
                        'location'  => $this->branch->location,
                        'phone'     => $this->branch->phone,
                        'telegram'  => $this->branch->telegram,
                        'opened_hour'  => $this->branch->opening_hour,
                        'closed_hour'  => $this->branch->close_hour,
                        'image'     => $this->branch->image_url,
                        'cover'     => $this->branch->cover_url,
                        'status'    => $this->branch?->status,
                    ] : null,
                    'bonus_items'   => $variate->product->bonusItems->map(function ($bonus) {
                        return [
                            'title'     => $bonus?->title,
                            'image_url' => $bonus?->image_url,
                            'quantity'  => $bonus?->pivot?->amount_required,
                            'unit'      => $bonus?->item?->unit?->title,
                        ];
                    })
                ];
            })
        ];
    }
}
