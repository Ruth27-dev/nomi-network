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
        $id = $this->id ?: null;
        return [
            'title_en'        => 'required',
            'status'          => 'required',
            'parent_id'       => 'nullable|exists:categories,id',
            'slug'            => 'required|unique:categories,slug,' . $id,
        ];
    }

    public function messages()
    {
        return [
            'title_en.required'         => __('validate.attributes.required'),
            'status.required'           => __('validate.attributes.required'),
            'slug.required'             => __('validate.attributes.required'),
            'slug.unique'               => __('validate.attributes.unique'),
        ];
    }

    public function validate($rules = null, ...$params)
    {
        if ($rules === null) {
            return parent::validate($this->rules(), ...$params);
        }
        return parent::validate($rules, ...$params);
    }
}
