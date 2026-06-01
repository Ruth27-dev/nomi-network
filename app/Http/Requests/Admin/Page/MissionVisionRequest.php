<?php

namespace App\Http\Requests\Admin\Page;

use Illuminate\Foundation\Http\FormRequest;

class MissionVisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'type'           => 'required|in:MISSION,VISION',
            'title_en'       => 'required|string|max:255',
            'title_km'       => 'nullable|string|max:255',
            'description_en' => 'required|string',
            'description_km' => 'nullable|string',
            'sequence'       => 'required|integer',
            'status'         => 'required',
            'images'         => 'nullable|array',
            'images.*'       => 'nullable|image',
            'tmp_images'     => 'nullable|array',
            'tmp_images.*'   => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'           => __('validate.attributes.required'),
            'title_en.required'       => __('validate.attributes.required'),
            'description_en.required' => __('validate.attributes.required'),
            'sequence.required'       => __('validate.attributes.required'),
            'sequence.integer'        => __('validate.attributes.numeric'),
            'status.required'         => __('validate.attributes.required'),
        ];
    }
}
