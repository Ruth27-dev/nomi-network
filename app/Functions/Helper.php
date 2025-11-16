<?php

use Carbon\Carbon;
use Illuminate\Support\Number;

function routeActive(string $route)
{
    $arr = explode(',', $route);
    foreach ($arr as $item) {
        if (request()->is("$item")) {
            return true;
        }
    }
    return false;
}

function displayUsdCurrency($amount, bool $showZeroWhenEmpty = true): string
{
    // treat null/'' as empty
    if ($amount === null || $amount === '') {
        return $showZeroWhenEmpty ? Number::currency(0, 'USD') : '';
    }

    if (is_string($amount)) {
        $amount = preg_replace('/[^\d\.\-]/', '', $amount);
    }

    $num = is_numeric($amount) ? (float) $amount : 0.0;

    return Number::currency($num, 'USD');
}

function dateTimeFormat($date)
{
    return Carbon::make($date)->format('m/d/Y h:i A');
}

function calcOrderTotalPrice($data)
{
    $percentage = config('dummy.discount.type.percentage');
    $amount_type = config('dummy.discount.type.amount');
    $total = [
        'sub_total'         => $data->total_price,
        'total_price'       => $data->total_price,
        'total_discount'    => 0,
        'details'           => [],
    ];
    if ($data) {
        if ($data->discountUsage) {
            $type = $data?->discountUsage?->discount_data['type'];
            $amount = $data?->discountUsage?->discount_data['amount'];
            if ($type == $percentage) $total['total_discount'] = ($data->total_price * $amount) / 100;
            if ($type == $amount_type) $total['total_discount'] = $amount;

            $total['total_price'] = $data?->total_price - $total['total_discount'];
        } else {
            foreach ($data->details as $detail) {
                if ($detail->discountUsage) {
                    $type = $detail?->discountUsage?->discount_data['type'];
                    $amount = $detail?->discountUsage?->discount_data['amount'];
                    $is_flat_discount = $detail?->discountUsage?->discount_data['is_flat_discount'];
                    $total_price = $is_flat_discount ? $detail?->unit_price : ($detail?->unit_price * $detail?->quantity);
                    $total_discount = 0;
                    if ($type == $percentage) $total_discount = ($total_price * $amount) / 100;
                    if ($type == $amount_type) $total_discount = $amount;

                    $total['details'][$detail->id] = [
                        'sub_total'         => $detail?->unit_price * $detail?->quantity,
                        'total_price'       => $is_flat_discount ? (($total_price * $detail?->quantity) - $total_discount) : ($total_price - $total_discount),
                        'total_discount'    => $total_discount
                    ];
                    $total['total_discount'] += $total_discount;
                }
            }
            $total['total_price'] = $data->total_price - $total['total_discount'];
        }
    }

    return $total;
}

if (! function_exists('khmer_number')) {
    function khmer_number(?string $number): string
    {
        if (!$number) return '';

        $map = [
            '0' => '០',
            '1' => '១',
            '2' => '២',
            '3' => '៣',
            '4' => '៤',
            '5' => '៥',
            '6' => '៦',
            '7' => '៧',
            '8' => '៨',
            '9' => '៩',
        ];

        // Replace only the Latin digits; leave other characters unchanged
        return strtr($number, $map);
    }
}
