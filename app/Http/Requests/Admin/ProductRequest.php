<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $id = $this->id ?? null;
        return [
            'code'              => 'nullable|unique:products,code,' . $id,
            'title_en'          => 'required',
            'status'            => 'required',
            'category_ids'      => 'required|array',

            'product_variates.*.title_en'  => 'required',
            'product_variates.*.title_km'  => 'required',
            'product_variates.*.price'     =>  'required',
        ];
    }

    public function messages()
    {
        return [
            'code.required'                             => __('validate.attributes.required'),
            'code.unique'                               => __('validate.attributes.unique'),
            'title_en.required'                         => __('validate.attributes.required'),
            'title_km.required'                         => __('validate.attributes.required'),
            'description_en.required'                   => __('validate.attributes.required'),
            'description_km.required'                   => __('validate.attributes.required'),
            'status.required'                           => __('validate.attributes.required'),
            'category_ids.required'                     => __('validate.attributes.required'),
            'product_variates.*.title_en.required'      => __('validate.attributes.required'),
            'product_variates.*.title_km.required'      => __('validate.attributes.required'),
            'product_variates.*.price.required'         => __('validate.attributes.required'),
        ];
    }


    public function validate($rules, ...$params)
    {
        return parent::validate($rules, ...$params);
    }
}
