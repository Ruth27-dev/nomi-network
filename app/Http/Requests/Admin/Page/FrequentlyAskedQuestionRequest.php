<?php

namespace App\Http\Requests\Admin\Page;

use Illuminate\Foundation\Http\FormRequest;

class FrequentlyAskedQuestionRequest extends FormRequest
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
    public function rules()
    {
        return [
            'title_km'          => 'required',
            'title_en'          => 'required',
            'short_detail_km'   => 'required',
            'short_detail_en'   => 'required',
            'status'            => 'required',


            'dataDetail'                       => 'required',
            'dataDetail.*.question_km'            => 'required',
            'dataDetail.*.question_en'            => 'required',
            'dataDetail.*.answer_km'      => 'required',
            'dataDetail.*.answer_en'      => 'required',
            'dataDetail.*.ordering'            => 'required',


        ];
    }

    public function messages()
    {
        return [
            'title_km.required'                         => __('validate.attributes.title_km'),
            'title_en.required'                         => __('validate.attributes.title_en'),
            'short_detail_km.required'                  => __('validate.attributes.content_km'),
            'short_detail_en.required'                  => __('validate.attributes.content_en'),
            'status.required'                           => __('validate.attributes.status'),

            'dataDetail.*.question_km.required'            => __('validate.attributes.question_km'),
            'dataDetail.*.question_en.required'            => __('validate.attributes.question_en'),
            'dataDetail.*.answer_km.required'      => __('validate.attributes.answer_km'),
            'dataDetail.*.answer_en.required'      => __('validate.attributes.answer_en'),
            'dataDetail.*.ordering.required'            => __('validate.attributes.ordering'),
        ];
    }
}
