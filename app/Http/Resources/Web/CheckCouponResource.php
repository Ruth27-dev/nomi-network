<?php

namespace App\Http\Resources\Web;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckCouponResource extends JsonResource
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
            'type'              => $this->type,
            'code'              => $this->code,
            'discount_amount'   => $this->discount_amount,
            'discount_type'     => $this->discount_type,
            'start_date'        => $this->start_date ? Carbon::make($this->start_date)->format('d/m/Y') : null,
            'end_date'          => $this->end_date ? Carbon::make($this->end_date)->format('d/m/Y') : null,
            'usage_limit'       => $this->usage_limit,
            'status'            => $this->status,
            'rules'             => $this->conditions->map(function($condition){
                return [
                    'type'      => $condition->type,
                    'amount'    => $condition->amount,
                ];
            }),
            'products'          => $this->products->map(function($product){
                return [
                    'id'    => $product->id,
                    'title' => $product->itemVariate->title,
                    'price' => $product->price,
                ];
            }),
        ];
    }
}