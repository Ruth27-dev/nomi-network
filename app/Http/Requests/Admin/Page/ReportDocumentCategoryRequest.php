<?php

namespace App\Http\Requests\Admin\Page;

use Illuminate\Foundation\Http\FormRequest;

class ReportDocumentCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'title_en' => 'required|string|max:255',
            'title_km' => 'nullable|string|max:255',
            'sequence' => 'required|integer',
            'status'   => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'title_en.required' => __('validate.attributes.required'),
            'sequence.required' => __('validate.attributes.required'),
            'sequence.integer'  => __('validate.attributes.numeric'),
            'status.required'   => __('validate.attributes.required'),
        ];
    }
}

