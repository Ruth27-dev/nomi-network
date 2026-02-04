@extends('admin::shared.layout')
@section('style')
    <link rel="stylesheet" href="{{ asset('plugin/css/form.css') }}">
    <style>
        .map {
            width: 100%;
            height: 300px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #d8dce5;
        }

        .map::before {
            content: "Paste Google Embed map";
            color: #fff;
            text-align: center;
            padding-top: 150px;
            position: absolute;
            top: 0;
            bottom: 0;
            z-index: 1;
            background-color: rgba(51, 51, 51, 0.788);
            width: 100%;
            height: 100%;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.4s ease-in-out;
        }

        iframe {
            width: 100%;
            height: 100%;
        }
    </style>
@endsection
@section('layout')
    <div class="form-admin" x-data="frequentlyAskedQuestionPage">
        @include('admin::shared.header', [
            'title' => __('form.name.frequently_asked_question'),
            'header_name' => __('form.name.frequently_asked_question'),
        ])
        <form id="form" class="form-wrapper">
            <div class="form-header"></div>
            <div class="form-body">
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.title_en')<span>*</span></label>
                        <input type="text" placeholder="@lang('form.body.placeholder.title_en')" min="8" id="title_en"
                            x-model="form.title_en" autocomplete="off">
                        <span class="error" x-show="validate?.title_en" x-text="validate?.title_en"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.title_km')<span>*</span></label>
                        <input type="text" placeholder="@lang('form.body.placeholder.title_km')" min="8" id="title_km"
                            x-model="form.title_km" autocomplete="off">
                        <span class="error" x-show="validate?.title_km" x-text="validate?.title_km"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.content_en')<span>*</span> </label>
                        <textarea x-model="form.short_detail_en" rows="1" placeholder="@lang('form.body.placeholder.content_en')"></textarea>
                        <span class="error" x-show="validate?.short_detail_en" x-text="validate?.short_detail_en"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.content_km')<span>*</span> </label>
                        <textarea x-model="form.short_detail_km" rows="1" placeholder="@lang('form.body.placeholder.content_km')"></textarea>
                        <span class="error" x-show="validate?.short_detail_km" x-text="validate?.short_detail_km"></span>
                    </div>
                </div>
                <fieldset class="border-[#d8dce5] border rounded p-3 mb-3">
                    <legend>@lang('table.option.detail')</legend>
                    <div class="row flex flex-col gap-3">
                        <template x-for="(item, index) in dataDetail">
                            <div class="row-2 border-[#d8dce5] border rounded p-3 relative" :key="index"
                                x-data="feather.replace()">
                                <!-- Add / Remove buttons -->
                                <div @click="addRow()" x-show="index == 0"
                                    class="flex justify-center items-center w-[24px] h-[24px] rounded-[50%] cursor-pointer bg-green-500 text-white absolute top-[-12px] right-[-12px]">
                                    <i class="w-[20px] h-[20px]" data-feather="plus"></i>
                                </div>
                                <div @click="removeRow(index)" x-show="index != 0"
                                    class="flex justify-center items-center w-[24px] h-[24px] rounded-[50%] cursor-pointer bg-rose-400 text-white absolute top-[-12px] right-[-12px]">
                                    <i class="w-[20px] h-[20px]" data-feather="x"></i>
                                </div>

                                <!-- question_en -->
                                <div class="form-row">
                                    <label>@lang('form.body.label.question_en') <span>*</span></label>
                                    <input type="text" x-model="item.question_en" :disabled="form.disabled"
                                        placeholder="@lang('form.body.placeholder.question_en')" autocomplete="off">
                                    <span class="error" x-show="validate?.[`dataDetail.${index}.question_en`]"
                                        x-text="validate?.[`dataDetail.${index}.question_en`]"></span>
                                </div>

                                <!-- question_km -->
                                <div class="form-row">
                                    <label>@lang('form.body.label.question_km') <span>*</span></label>
                                    <input type="text" x-model="item.question_km" :disabled="form.disabled"
                                        placeholder="@lang('form.body.placeholder.question_km')" autocomplete="off">
                                    <span class="error" x-show="validate?.[`dataDetail.${index}.question_km`]"
                                        x-text="validate?.[`dataDetail.${index}.question_km`]"></span>
                                </div>

                                <!-- answer_en -->
                                <div class="form-row">
                                    <label>@lang('form.body.label.answer_en') <span>*</span></label>
                                    <textarea rows="2" x-model="item.answer_en" placeholder="@lang('form.body.placeholder.answer_en')"></textarea>
                                    <span class="error" x-show="validate?.[`dataDetail.${index}.answer_en`]"
                                        x-text="validate?.[`dataDetail.${index}.answer_en`]"></span>
                                </div>

                                <!-- answer_km -->
                                <div class="form-row">
                                    <label>@lang('form.body.label.answer_km') <span>*</span></label>
                                    <textarea rows="2" x-model="item.answer_km" placeholder="@lang('form.body.placeholder.answer_km')"></textarea>
                                    <span class="error" x-show="validate?.[`dataDetail.${index}.answer_km`]"
                                        x-text="validate?.[`dataDetail.${index}.answer_km`]"></span>
                                </div>
                                <div class="form-row">
                                    <label>@lang('form.body.label.ordering') <span>*</span></label>
                                    <input type="number" x-model="item.ordering" :disabled="form.disabled"
                                        placeholder="@lang('form.body.placeholder.ordering')" autocomplete="off">
                                    <span class="error" x-show="validate?.[`dataDetail.${index}.ordering`]"
                                        x-text="validate?.[`dataDetail.${index}.ordering`]"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </fieldset>
                <div class="form-button">
                    @can('frequently-asked-question-update')
                        <button type="button" @click="onSave()" :disabled="form.disabled || loading" color="primary"
                            class="!rounded-[50px]">
                            <span class="material-icons mr-1">save</span>
                            <span>Save</span>
                            <div class="loader" style="display: none" x-show="loading"></div>
                        </button>
                    @endcan
                </div>
            </div>
            <div class="form-footer"></div>
        </form>
    </div>
@stop
@section('script')
    <script src="{{ asset('plugin/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    <script type="module">
        Alpine.data('frequentlyAskedQuestionPage', () => ({
            form: new FormGroup({
                page: ['frequently_asked_question', ['required']],
                title_en: [null, ['required']],
                title_km: [null, ['required']],
                short_detail_en: [null, ['required']],
                short_detail_km: [null, ['required']],
                status: ['ACTIVE', ['required']],
            }),
            dataDetail: [{
                question_en: null,
                question_km: null,
                answer_en: null,
                answer_km: null,
                ordering: null,
            }],
            id: null,
            validate: null,
            loading: false,
            async init() {
                feather.replace();
                let data = @json($page);
                if (data) {
                    this.id = data.id;
                    this.form.page = data?.page;
                    this.form.title_en = data?.title?.en;
                    this.form.title_km = data?.title?.km;
                    this.form.short_detail_en = data?.short_detail?.en;
                    this.form.short_detail_km = data?.short_detail?.km;

                    this.dataDetail = (data?.content?.dataDetail || []).map(item => ({
                        ...item,
                    }));

                }
            },
            onAddMap() {
                this.$store.addMapDialog.open({
                    data: {
                        title: "Add Map",
                    },
                    afterClosed: (result) => {
                        if (result) {
                            this.form.embed_map = result.embedLink;
                        }
                    }
                });
            },
            addRow() {
                this.dataDetail.push({
                    question_en: null,
                    question_km: null,
                    answer_en: null,
                    answer_km: null,
                    ordering: null,
                });
            },
            removeRow(index) {
                this.dataDetail.splice(index, 1);
                if (this.dataDetail.length == 1) {
                    this.dataDetail[0].amount = null;
                }
            },

            onSave() {
                this.$store.confirmDialog.open({
                    data: {
                        title: "@lang('dialog.title')",
                        message: "@lang('dialog.msg.save')",
                        btnClose: "@lang('dialog.button.close')",
                        btnSave: "@lang('dialog.button.save')",
                    },
                    afterClosed: async (result) => {
                        if (!result) return;

                        this.form.disable();
                        this.loading = true;

                        const formData = new FormData();
                        const formValue = this.form.value();

                        // Append main form fields
                        for (const key in formValue) {
                            formData.append(key, formValue[key]);
                        }


                        if (this.id !== null && this.id !== 'null') {
                            formData.append('id', this.id);
                        }

                        // Append details including file
                        this.dataDetail.forEach((item, index) => {
                            formData.append(`dataDetail[${index}][question_en]`, item
                                .question_en ?? '');
                            formData.append(`dataDetail[${index}][question_km]`, item
                                .question_km ?? '');
                            formData.append(`dataDetail[${index}][answer_en]`, item
                                .answer_en ?? '');
                            formData.append(`dataDetail[${index}][answer_km]`, item
                                .answer_km ?? '');
                            formData.append(`dataDetail[${index}][ordering]`, item
                                .ordering ?? '');
                        });

                        Axios.post(`{{ route('admin-page-frequently-asked-question-save') }}`, formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }).then((res) => {
                            this.id = res.data.id;
                            Toast({
                                message: res.data.message,
                                status: res.data.status,
                                size: 'small',
                            });
                        }).catch((e) => {
                            this.validate = e.response?.data?.errors;
                        }).finally(() => {
                            this.form.enable();
                            this.loading = false;
                        });
                    }
                });
            }
        }));
    </script>
@endsection
