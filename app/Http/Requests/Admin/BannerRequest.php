<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
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
            'title_en'    => 'required',
            'banner_page' => 'required',
            'ordering'    => 'required|integer',
            'status'      => 'required',
            'image'       => 'required_without:tmp_file',
        ];
    }

    public function messages()
    {
        return [
            'title_en.required'    => __('validate.attributes.required'),
            'banner_page.required' => __('validate.attributes.required'),
            'ordering.required'    => __('validate.attributes.required'),
            'ordering.integer'     => __('validate.attributes.numeric'),
            'status.required'      => __('validate.attributes.required'),
            'image.required_without' => __('validate.attributes.required'),
        ];
    }
}
