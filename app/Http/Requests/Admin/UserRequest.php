<?php

namespace App\Http\Requests\Admin;

use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
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

        $id = $this->id ?? '';
        return [
            'name'                  => 'required|max:100',
            'email' => $this->type == config('dummy.user.type.driver') || $this->type == config('dummy.user.type.user')
                ? 'nullable|email|max:255|unique:users,email,' . $id
                : 'required|email|max:255|unique:users,email,' . $id,
            'phone'                 => 'required|max:20|unique:users,phone,' . $id,
            'role_id'               => $this->role == 'admin' ? 'required' : 'nullable',
            'password'              => $id ? 'nullable|max:20' : 'required|max:20',
            'password_confirmation' => $id ? 'nullable' : 'required|same:password',
        ];
    }

    public function messages()
    {
        return [
            'name.required'                     => __('validate.attributes.required'),
            'name.max'                          => __('validate.attributes.required'),
            'role_id.required'                  => __('validate.attributes.required'),
            'email.required'                    => __('validate.attributes.required'),
            'email.unique'                      => __('validate.attributes.required'),
            'gender.required'                   => __('validate.attributes.required'),
            'phone.required'                    => __('validate.attributes.required'),
            'phone.max'                         => __('validate.attributes.max'),
            'phone.unique'                      => __('validate.attributes.unique'),
            'password.required'                 => __('validate.attributes.password'),
            'password.max'                      => __('validate.attributes.required'),
            'password_confirmation.required'    => __('validate.attributes.required'),
            'password_confirmation.same'        => __('validate.attributes.required'),
        ];
    }
}
