<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderHistoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data'  => $this->collection->map(function ($item) {
                $discount = calcOrderTotalPrice($item);
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
                    'payment'           => $item->payment ? [
                        'id'            => $item->payment->id,
                        'payment_type'  => $item->payment->paymentType ? [
                            'id'        => $item->payment->paymentType->id,
                            'title'     => $item->payment->paymentType->title,
                        ] : null,
                        'payment_method' => $item->payment->paymentMethod ? [
                            'id'        => $item->payment->paymentMethod->id,
                            'title'     => $item->payment->paymentMethod->title,
                        ] : null,
                        'amount'        => $item->payment->amount,
                        'status'        => $item->payment->status,
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
            }),
            'paginate' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage(),
                'next_page_url' => $this->nextPageUrl(),
                'prev_page_url' => $this->previousPageUrl(),
            ],
        ];
    }
}
