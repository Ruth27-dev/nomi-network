<?php

namespace App\Http\Requests\Api\Web;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $email = $this->input('email');

        $this->merge([
            'first_name' => $this->input('first_name', $this->input('firstName')),
            'last_name' => $this->input('last_name', $this->input('lastName')),
            'confirm_password' => $this->input('confirm_password', $this->input('password_confirmation')),
            'agree_terms' => $this->input('agree_terms', $this->input('terms_accepted', $this->input('is_agree'))),
            'receive_updates' => $this->input('receive_updates', $this->input('is_subscribed')),
            'email' => is_string($email) ? strtolower(trim($email)) : $email,
        ]);
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required_without:name|string|max:100',
            'last_name' => 'required_without:name|string|max:100',
            'name' => 'required_without_all:first_name,last_name|string|max:200',
            'email' => 'required_without:phone|nullable|email|unique:users,email',
            'phone' => 'required_without:email|nullable|unique:users,phone',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
            'agree_terms' => 'required|accepted',
            'receive_updates' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered.',
        ];
    }
}
