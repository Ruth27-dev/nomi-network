<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => 'required',
            'new_password' => 'required',
            'confirm_new_password' => 'required|same:new_password',
        ];
    }

    public function messages()
    {
        return [
            'id.required'                   => __('validate.attributes.required'),
            'new_password.required'         => __('validate.attributes.required'),
            'confirm_new_password.required' => __('validate.attributes.required'),
            'confirm_new_password.same'     => __('validate.same', ['attribute' => __('validate.attributes.new_password')]),
        ];
    }
}
