<?php

namespace App\Http\Requests\Admin\Page;

use Illuminate\Foundation\Http\FormRequest;

class AboutUsRequest extends FormRequest
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
            'title_km.required'     =>  __('validate.attributes.required'),
            'title_en.required'     =>  __('validate.attributes.required'),
            'content_km.required'   =>  __('validate.attributes.required'),
            'content_en.required'   =>  __('validate.attributes.required'),
            'status.required'       =>  __('validate.attributes.required'),
        ];
    }
}
