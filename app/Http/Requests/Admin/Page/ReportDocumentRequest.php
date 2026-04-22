<?php

namespace App\Http\Requests\Admin\Page;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'title_en'    => 'required|string|max:255',
            'title_km'    => 'nullable|string|max:255',
            'category_id' => [
                'required',
                'integer',
                Rule::exists('list_of_values', 'id')->where(function ($query) {
                    $query->where('type', config('dummy.module.report_document_category.key'));
                }),
            ],
            'date'        => 'required|date_format:d/m/Y',
            'file'        => 'required_without:tmp_file|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240',
            'sequence'    => 'required|integer',
            'status'      => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'title_en.required'          => __('validate.attributes.required'),
            'category_id.required'       => __('validate.attributes.required'),
            'date.required'              => __('validate.attributes.required'),
            'date.date_format'           => __('validate.attributes.date_format'),
            'file.required_without'      => __('validate.attributes.required'),
            'file.file'                  => __('validate.attributes.file'),
            'file.mimes'                 => __('validate.attributes.mimes'),
            'sequence.required'          => __('validate.attributes.required'),
            'sequence.integer'           => __('validate.attributes.numeric'),
            'status.required'            => __('validate.attributes.required'),
        ];
    }
}

