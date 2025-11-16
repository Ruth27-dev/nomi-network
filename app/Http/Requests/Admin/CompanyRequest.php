<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name_en'         => 'required',
            'phone_en'        => 'required',
            'logo'            => !$this->tmp_file ? 'required' : 'nullable',
            'status'          => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name_en.required'          => __('validate.attributes.required'),
            'address_en.required'       => __('validate.attributes.required'),
            'phone_en.required'            => __('validate.attributes.required'),
            'phone_km.required'            => __('validate.attributes.required'),
            'vat_tin.required'          => __('validate.attributes.required'),
            'logo.required'             => __('validate.attributes.required'),
            'status.required'           => __('validate.attributes.required'),
        ];
    }
}
