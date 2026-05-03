<?php

namespace App\Http\Requests\Admin\Page;

use Illuminate\Foundation\Http\FormRequest;

class CareerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'position_en' => 'required|string|max:255',
            'position_km' => 'nullable|string|max:255',
            'location_en'    => 'required|string|max:255',
            'location_km'    => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_km' => 'nullable|string',
            'close_date'     => 'required|date_format:d/m/Y',
            'sequence'    => 'required|integer',
            'status'      => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'position_en.required' => __('validate.attributes.required'),
            'location_en.required' => __('validate.attributes.required'),
            'close_date.required'  => __('validate.attributes.required'),
            'close_date.date_format' => __('validate.attributes.date_format'),
            'sequence.required'    => __('validate.attributes.required'),
            'sequence.integer'     => __('validate.attributes.numeric'),
            'status.required'      => __('validate.attributes.required'),
        ];
    }
}
