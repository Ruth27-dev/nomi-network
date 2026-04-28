<?php

namespace App\Http\Requests\Admin\Page;

use Illuminate\Foundation\Http\FormRequest;

class ProductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules()
    {
        return [
            'status'                         => 'required',
            'dataDetail'                     => 'nullable|array',
            'dataDetail.*.description_en'    => 'required',
            'dataDetail.*.description_km'    => 'required',
            'dataDetail.*.ordering'          => 'required|integer',
            'dataDetail.*.image'             => 'nullable|image',

            'imageSlides'                    => 'nullable|array',
            'imageSlides.*.ordering'         => 'required|integer',
            'imageSlides.*.image'            => 'nullable|image',
        ];
    }

    public function messages()
    {
        return [
            'status.required'                        => __('validate.attributes.required'),
            'dataDetail.*.description_en.required'   => __('validate.attributes.required'),
            'dataDetail.*.description_km.required'   => __('validate.attributes.required'),
            'dataDetail.*.ordering.required'         => __('validate.attributes.required'),
            'dataDetail.*.ordering.integer'          => __('validate.attributes.numeric'),
            'imageSlides.*.ordering.required'        => __('validate.attributes.required'),
            'imageSlides.*.ordering.integer'         => __('validate.attributes.numeric'),
        ];
    }
}
