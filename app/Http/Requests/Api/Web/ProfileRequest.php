<?php

namespace App\Http\Requests\Api\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('api_web')->check();
    }

    public function rules(): array
    {
        $id = Auth::guard('api_web')->id();

        return [
            'name' => 'required',
            'phone' => 'required|unique:users,phone,' . $id,
        ];
    }
}
