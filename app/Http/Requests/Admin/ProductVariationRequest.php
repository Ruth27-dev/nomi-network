<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $id = $this->id ?? null;
        return [
            'product_id' => 'required|exists:products,id',
            'sku' => 'required|unique:product_variations,sku,' . $id,
            'title_en' => 'required',
            'price' => 'required|numeric',
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => __('validate.attributes.required'),
            'product_id.exists' => __('validate.attributes.required'),
            'sku.required' => __('validate.attributes.required'),
            'sku.unique' => __('validate.attributes.unique'),
            'title_en.required' => __('validate.attributes.required'),
            'price.required' => __('validate.attributes.required'),
            'price.numeric' => __('validate.attributes.required'),
            'status.required' => __('validate.attributes.required'),
        ];
    }

    public function validate($rules, ...$params)
    {
        return parent::validate($rules, ...$params);
    }
}
