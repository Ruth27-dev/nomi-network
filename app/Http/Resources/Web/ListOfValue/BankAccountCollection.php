<?php

namespace App\Http\Resources\Web\ListOfValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BankAccountCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'branch_id' => $item->branch_id,
                'bank_name' => $item->bank_name,
                'bank_number' => $item->bank_number,
                'account_name' => $item->account_name,
                'ordering' => $item->ordering,
                'qr_code_url' => $item->qr_code
                    ? $item->qr_code_url
                    : asset('images/no.jpg'),
                'ordering' => $item->ordering,
            ];
        })->toArray();
    }
}
