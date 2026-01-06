<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DiscountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type'              => 'required|in:DISCOUNT,COUPON',
            'code'              => $this->type == 'COUPON' ? 'required|unique:discounts,code,' . $this->id : 'nullable|unique:discounts,code,' . $this->id,
            'status'            => 'required|in:ACTIVE,INACTIVE',
            'title_en'          => 'required',
            'discount_amount'   => 'required|numeric|decimal:0,2',
            'discount_type'     => 'required|in:AMOUNT,PERCENTAGE',
            'usage_limit'       => 'nullable|numeric|integer',
            'usage_per_customer'=> 'nullable|numeric|integer',
            'min_amount'        => 'nullable|numeric|decimal:0,2',
            'max_amount'        => 'nullable|numeric|decimal:0,2',
            'product_ids'       => 'nullable|array',
        ];
    }

    public function messages()
    {
        return [
            'type.required'            => __('validate.attributes.required'),
            'code.required'            => __('validate.attributes.required'),
            'status.required'          => __('validate.attributes.required'),
            'title_en.required'        => __('validate.attributes.required'),
            'title_km.required'        => __('validate.attributes.required'),
            'discount_amount.required' => __('validate.attributes.required'),
            'discount_type.required'   => __('validate.attributes.required'),
        ];
    }
}
