<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SocialMediaRequest extends FormRequest
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
    public function rules()
    {
        return [
            'title_en'      => 'required',
            'title_km'      => 'required',
            'ordering'      => 'required|integer',
            'image'         => !$this->tmp_file ? 'required|image' : 'nullable|image',
            'status'        => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title_en.required'       => __('validate.attributes.required'),
            'title_km.required'       => __('validate.attributes.required'),
            'ordering.required'       => __('validate.attributes.required'),
            'ordering.integer'        => __('validate.attributes.numeric'),
            'image.required'          => __('validate.attributes.required'),
            'status.required'         => __('validate.attributes.required'),
        ];
    }
}
