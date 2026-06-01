<?php

namespace App\Http\Requests\Admin\Page;

use Illuminate\Foundation\Http\FormRequest;

class OurProgramRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules()
    {
        return [
            'title_km'          => 'required',
            'title_en'          => 'required',
            'short_detail_km'   => 'required',
            'short_detail_en'   => 'required',
            'status'            => 'required',


            'dataDetail'                       => 'nullable|array',
            'dataDetail.*.title_km'            => 'required',
            'dataDetail.*.title_en'            => 'required',
            'dataDetail.*.description_km'      => 'required',
            'dataDetail.*.description_en'      => 'required',
            'dataDetail.*.ordering'            => 'required',
            'dataDetail.*.images'              => 'nullable|array',
            'dataDetail.*.images.*'            => 'nullable|image',
            'dataDetail.*.icon'                => 'nullable|image',


        ];
    }

    public function messages()
    {
        return [
            'title_km.required'                         => __('validate.attributes.required'),
            'title_en.required'                         => __('validate.attributes.required'),
            'short_detail_km.required'                  => __('validate.attributes.required'),
            'short_detail_en.required'                  => __('validate.attributes.required'),
            'status.required'                           => __('validate.attributes.required'),

            'dataDetail.*.title_km.required'            => __('validate.attributes.required'),
            'dataDetail.*.title_en.required'            => __('validate.attributes.required'),
            'dataDetail.*.description_km.required'      => __('validate.attributes.required'),
            'dataDetail.*.description_en.required'      => __('validate.attributes.required'),
            'dataDetail.*.ordering.required'            => __('validate.attributes.required'),
        ];
    }
}
