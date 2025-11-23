<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CategoryRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'title_en'        => 'required',
            'sequence'        => 'required|integer',
            'status'          => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title_en.required'         => __('validate.attributes.required'),
            'status.required'           => __('validate.attributes.required'),
            'sequence.required'         => __('validate.attributes.required'),
            'sequence.integer'          => __('validate.attributes.numeric'),
        ];
    }
}
