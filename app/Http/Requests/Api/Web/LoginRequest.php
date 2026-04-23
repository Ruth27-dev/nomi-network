<?php

namespace App\Http\Requests\Api\Web;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required_without:phone|nullable|email',
            'phone' => 'required_without:email|nullable',
            'password' => 'required',
        ];
    }
}
