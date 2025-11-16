<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $item = $this;
        $discount = calcOrderTotalPrice($item);
        $driver = $item->deliveries->first();
        return [
            'id'                => $item?->id,
            'code'              => $item?->code,
            'name'              => $item?->name,
            'phone'             => $item?->phone,
            'address'           => $item?->address,
            'status'            => $item?->status,
            'location'          => $item?->location,
            'order_date'        => $item?->order_date ? dateTimeFormat($item?->order_date) : null,
            'scheduled_date'    => $item?->scheduled_date ? dateTimeFormat($item?->scheduled_date) : null,
            'remark'            => $item?->remark,
            'total_quantity'    => $item?->total_quantity,
            'full_price'        => $item?->total_price,
            'total_discount'    => $discount['total_discount'],
            'delivery_fee'      => $item?->delivery_fee,
            'total_price'       => $discount['total_price'] + $item?->delivery_fee,
            'branch'            => $item?->branch,
            'delivery'          => $item?->delivery,
            'discount'          => $item?->discountUsage?->discount_data,
            'payments' => $item->payment->map(function ($p) {
                return [
                    'id' => $p->id,
                    'payment_type' => $p->paymentType ? [
                        'id'    => $p->paymentType->id,
                        'title' => $p->paymentType->title,
                    ] : null,
                    'payment_method' => $p->paymentMethod ? [
                        'id'    => $p->paymentMethod->id,
                        'title' => $p->paymentMethod->title,
                    ] : null,
                    'remark' => $p->remark,
                    'amount' => $p->amount,
                    'status' => $p->status,
                ];
            }),
            'driver_hero'       => $driver ? [
                'id'      => $driver->id,
                'name'    => $driver->name,
                'phone'   => $driver->phone,
                'image'   => $driver->profile_url,
            ] : null,
            'details'           => $item?->details->map(function ($detail) use ($discount) {
                return [
                    'id'                    => $detail?->id,
                    'product_variate_id'    => $detail?->product_variate_id,
                    'title'                 => $detail?->product?->itemVariate?->title,
                    'quantity'              => $detail?->quantity,
                    'unit_price'            => $detail?->unit_price,
                    'image_url'             => $detail?->product?->itemVariate?->image_url,
                    'full_price'            => $detail?->unit_price * $detail->quantity,
                    'total_price'           => $detail?->discountUsage ? $discount['details'][$detail->id]['total_price'] : ($detail?->unit_price * $detail->quantity),
                    'total_discount'        => $detail?->discountUsage ? $discount['details'][$detail->id]['total_discount'] : 0,
                    'discount'              => $detail?->discountUsage?->discount_data,
                ];
            }),
        ];
    }
}
