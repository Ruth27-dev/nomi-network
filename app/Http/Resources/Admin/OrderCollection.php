<?php

namespace App\Http\Resources\Admin;

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
                    'order_from'        => $item?->order_from,
                    'payments'          => $item?->payment,
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
                    'invoice'           =>  [
                        'id'             => $item?->invoice?->id,
                        'include_tax'    => $item?->invoice?->include_tax,
                        'order_id'    => $item?->invoice?->order_id,
                    ],


                ];
            }),
            'current_page'  => $this->currentPage(),
            'first_page_url' => $this->onFirstPage(),
            'from'          => $this->firstItem(),
            'last_page'     => $this->lastPage(),
            'last_page_url' => $this->onLastPage(),
            'next_page_url' => $this->nextPageUrl(),
            'path'          => $this->path(),
            'per_page'      => $this->perPage(),
            'prev_page_url' => $this->previousPageUrl(),
            'to'            => $this->lastItem(),
            'total'         => $this->total(),
            'total_pages'   => $this->lastPage(),


        ];
    }
}
