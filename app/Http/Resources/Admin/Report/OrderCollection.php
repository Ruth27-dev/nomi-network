<?php

namespace App\Http\Resources\Admin\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $transformed = $this->collection->map(function($item){
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
                'sub_total_price'   => $discount['total_price'],
                'total_price'       => $discount['total_price'] + $item?->delivery_fee,
                'total_discount'    => $discount['total_discount'],
                'branch'            => $item?->branch,
                'delivery'          => $item?->delivery,
                'discount'          => $item?->discountUsage?->discount_data,
                'payment'           => $item->payment ? [
                    'id'            => $item->payment->id,
                    'payment_type'  => $item->payment->paymentType ? [
                        'id'        => $item->payment->paymentType->id,
                        'title'     => $item->payment->paymentType->title,
                    ] : null,
                    'payment_method'=> $item->payment->paymentMethod ? [
                        'id'        => $item->payment->paymentMethod->id,
                        'title'     => $item->payment->paymentMethod->title,
                    ] : null,
                    'amount'        => $item->payment->amount,
                    'status'        => $item->payment->status,
                ] : null,
                'details'           => $item?->details->map(function($detail) use ($discount){
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
        });
        $otherData = [
            'total_sale_amount'     => $transformed->sum('total_price'),
            'total_order'           => $transformed->count(),
            'total_item_sale'       => $transformed->sum('total_quantity'),
        ];
        $otherData['aov'] = $otherData['total_order'] != 0 ? $otherData['total_sale_amount']/$otherData['total_order'] : 0;

        return [
            'data'      => $transformed,
            'otherData' => $otherData,
        ];
    }
}
