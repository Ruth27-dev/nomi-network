<?php

namespace App\Http\Requests\Admin\Page;

use Illuminate\Foundation\Http\FormRequest;

class AchievementSummaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'number'         => 'required|string|max:255',
            'title_en'       => 'required|string|max:255',
            'title_km'       => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_km' => 'nullable|string',
            'sequence'       => 'required|integer',
            'status'         => 'required',
            'image'          => 'required_without:tmp_file',
        ];
    }

    public function messages(): array
    {
        return [
            'number.required'         => __('validate.attributes.required'),
            'title_en.required'       => __('validate.attributes.required'),
            'sequence.required'       => __('validate.attributes.required'),
            'sequence.integer'        => __('validate.attributes.numeric'),
            'status.required'         => __('validate.attributes.required'),
            'image.required_without'  => __('validate.attributes.required'),
        ];
    }
}
