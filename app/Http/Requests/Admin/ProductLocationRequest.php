<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'name_en'       => 'required|string|max:255',
            'location_type' => 'required|in:province,city,district,other',
            'address'       => 'nullable|string',
            'status'        => 'required|in:ACTIVE,INACTIVE',
        ];
    }

    public function messages(): array
    {
        return [
            'name_en.required'       => __('validate.attributes.required'),
            'location_type.required' => __('validate.attributes.required'),
            'location_type.in'       => 'Invalid location type.',
            'status.required'        => __('validate.attributes.required'),
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
