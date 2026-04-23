<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BankAccountRequest extends FormRequest
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
            'bank_name'          => 'required',
            'bank_number'        => 'required',
            'account_name'       => 'required',
            'ordering'           => 'required|integer',
            'status'             => 'required',
        ];
    }

    public function messages()
    {
        return [
            'branch.required'        => __('validate.attributes.required'),
            'bank_name.required'     => __('validate.attributes.required'),
            'bank_number.required'   => __('validate.attributes.required'),
            'account_name.required'  => __('validate.attributes.required'),
            'status.required'        => __('validate.attributes.required'),
            'ordering.required'      => __('validate.attributes.required'),
            'ordering.integer'       => __('validate.attributes.numeric'),
        ];
    }


    public function validate($rules, ...$params)
    {
        return parent::validate($rules, ...$params);
    }
}
