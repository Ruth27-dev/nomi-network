<?php

namespace App\Http\Requests\Api\Web;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $email = $this->input('email');

        $this->merge([
            'email' => is_string($email) ? strtolower(trim($email)) : $email,
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => 'required_without:phone|nullable|email|exists:users,email',
            'phone' => 'required_without:email|nullable',
            'password' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'This email is not registered yet.',
        ];
    }
}
