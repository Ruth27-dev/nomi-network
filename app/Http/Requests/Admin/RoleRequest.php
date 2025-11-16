<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
            'display_name_en' => 'required',
            'status'          => 'required',
        ];
    }

    public function messages()
    {
        return [
            'display_name_en.required'          => __('validate.attributes.required'),
            'status.required'                   => __('validate.attributes.required'),
            
        ];
    }
}
