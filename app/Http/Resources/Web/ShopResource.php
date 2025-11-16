<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
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
            'title'             => $this->title,
            'phone'             => $this->phone,
            'telegram'          => $this->telegram,
            'image'             => $this->image_url,
            'status'            => $this->status,
            'count_categories'  => count($this->categories),
            'categories'        => $this->categories->map(function ($category) {
                return [
                    'id'        => $category->id,
                    'code'      => $category->code,
                    'title'     => $category->title,
                    'image'     => $category->image_url,
                    'description'   => $category->description,
                    'branch'    => [
                        'id'        => $category->branch_id,
                        'title'     => $category->branch->title,
                        'image'     => $category->branch->image_url,
                        'location'  => $category->branch->location,
                        'phone'     => $category->branch->phone,
                        'telegram'  => $category->branch->telegram,
                        'opened_hour'  => $category->branch->opening_hour,
                        'closed_hour'  => $category->branch->close_hour,
                        'image'     => $category->branch->image_url,
                        'cover'     => $category->branch->cover_url,
                        'status'    => $category->branch?->status,
                    ],
                    'items'     => $category->items->map(function ($item) {
                        return [
                            'id'            => $item->id,
                            'code'          => $item->code,
                            'title'         => $item->title,
                            'description'   => $item->description,
                            'image_url'     => $item->image_url,
                            'min_price'     => $item->itemVariates->pluck('product.price')->filter()->min(),
                            'max_price'     => $item->itemVariates->pluck('product.price')->filter()->max(),
                            'variates'      => $item->itemVariates->map(function ($variate) use ($item) {
                                return [
                                    'id'            => $variate->id,
                                    'product_id'    => $variate->product->id,
                                    'title'         => $variate->title,
                                    'image_url'     => $variate->image_url,
                                    'is_available'  => $variate->product->is_available,
                                    'is_pre_order'  => false,
                                    'description'   => $variate->product->description,
                                    'price'         => $variate->product->price,
                                    'cooking_duration' => $variate->product?->group['add_on']['cooking_duration'] ?? null,
                                    'note'          => $variate?->product?->is_note ?
                                        $variate->product->note :
                                        [
                                            'en'    => $item->unit && $variate?->product?->size ? $variate?->product?->size . ' ' . $item?->unit?->title['en'] : '',
                                            'km'    => $item->unit && $variate?->product?->size ? $variate?->product?->size . ' ' . $item?->unit?->title['km'] : '',
                                        ],
                                    'branch'    => $item->branch ? [
                                        'id'        => $item->branch->id,
                                        'title'     => $item->branch->title,
                                        'image'     => $item->branch->image_url,
                                        'location'  => $item->branch->location,
                                        'phone'     => $item->branch->phone,
                                        'telegram'  => $item->branch->telegram,
                                        'opened_hour'  => $item->branch->opening_hour,
                                        'closed_hour'  => $item->branch->close_hour,
                                        'image'     => $item->branch->image_url,
                                        'status'    => $item->branch?->status,
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
                    }),
                ];
            })
        ];
    }
}
