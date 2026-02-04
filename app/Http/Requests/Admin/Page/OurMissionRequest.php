<?php

namespace App\Http\Requests\Admin\Page;

use Illuminate\Foundation\Http\FormRequest;

class OurMissionRequest extends FormRequest
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
            'title_km'          => 'required',
            'title_en'          => 'required',
            'content_km'        => 'required',
            'content_en'        => 'required',
            'status'            => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title_km.required'     => __('validate.attributes.title_km'),
            'title_en.required'     => __('validate.attributes.title_en'),
            'content_km.required'   => __('validate.attributes.content_km'),
            'content_en.required'   => __('validate.attributes.content_en'),
            'status.required'       => __('validate.attributes.status'),
        ];
    }
}
