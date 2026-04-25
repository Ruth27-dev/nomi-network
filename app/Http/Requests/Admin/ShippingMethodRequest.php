<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ShippingMethodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title_en' => 'required',
            'title_km' => 'required',
            'price' => 'required|numeric',
            'ordering' => 'required|integer',
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title_en.required' => __('validate.attributes.required'),
            'title_km.required' => __('validate.attributes.required'),
            'price.required' => __('validate.attributes.required'),
            'price.numeric' => __('validate.attributes.numeric'),
            'status.required' => __('validate.attributes.required'),
            'ordering.required' => __('validate.attributes.required'),
            'ordering.integer' => __('validate.attributes.numeric'),
        ];
    }

    public function validate($rules, ...$params)
    {
        return parent::validate($rules, ...$params);
    }
}
