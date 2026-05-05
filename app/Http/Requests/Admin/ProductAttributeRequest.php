<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $id = $this->id ?? null;
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:product_attributes,code,' . $id,
            'input_type' => 'required|in:select,text,number,color,size,button',
            'status' => 'required|in:ACTIVE,INACTIVE',
            'is_variation' => 'nullable|boolean',
            'values' => 'required_if:input_type,select,color,size,button|array',
            'values.*' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('validate.attributes.required'),
            'sku.required' => __('validate.attributes.required'),
            'sku.unique' => __('validate.attributes.unique'),
            'input_type.required' => __('validate.attributes.required'),
            'status.required' => __('validate.attributes.required'),
            'values.required_if' => __('validate.attributes.required'),
            'values.*.required' => __('validate.attributes.required'),
        ];
    }

    public function validate($rules = null, ...$params)
    {
        if ($rules === null) {
            return parent::validate($this->rules(), ...$params);
        }
        return parent::validate($rules, ...$params);
    }
}
