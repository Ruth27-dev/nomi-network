@extends('admin::shared.layout')
@section('style')
    <link rel="stylesheet" href="{{ asset('plugin/css/form.css') }}">
@endsection
@section('layout')
    <div class="form-admin" x-data="aboutUsPage">
        @include('admin::shared.header', [
            'title' => __('form.name.about_us'),
            'header_name' => __('form.name.about_us'),
        ])
        <form id="form" class="form-wrapper">
            <div class="form-header"></div>
            <div class="form-body">
                <div class="form-header mb-0 !text-sm !flex !justify-end">
                    @include('admin::components.form-change-language')
                </div>
                <div class="flex justify-between gap-5">
                    <div class="w-[35%]">
                        <div class="row">
                            <div class="form-row" x-show="locale == arrayLangLocale.en ">
                                <label>@lang('form.body.label.title_en')<span>*</span> </label>
                                <textarea x-model="form.title_en" rows="3" placeholder="@lang('form.body.placeholder.title_en')"></textarea>
                                <span class="error" x-show="validate?.title_en" x-text="validate?.title_en"></span>
                            </div>
                            <div class="form-row" x-show="locale == arrayLangLocale.km">
                                <label>@lang('form.body.label.title_km')<span>*</span> </label>
                                <textarea x-model="form.title_km" rows="3" placeholder="@lang('form.body.placeholder.title_km')"></textarea>
                                <span class="error" x-show="validate?.title_km" x-text="validate?.title_km"></span>
                            </div>
                            <div class="form-row">
                                <label>@lang('form.body.label.status')<span>*</span> </label>
                                <select x-model="form.status" :disabled="form.disabled">
                                    @foreach (config('dummy.status') as $key => $status)
                                        <option value="{{ $status['key'] }}">{{ $status['text'] }}</option>
                                    @endforeach
                                </select>
                                <span class="error" x-show="validate?.status" x-text="validate?.status"></span>
                            </div>
                        </div>
                    </div>
                    <div class="w-[65%]">
                        <div class="row">
                            <div class="form-row" x-show="locale == arrayLangLocale.km">
                                <label>@lang('form.body.label.content_km')<span>*</span></label>
                                <textarea id="mytextarea-km" rows="24" class="!h-[500px]" placeholder="@lang('form.body.placeholder.content_km')"
                                    x-model="form.content_km"></textarea>
                                <span class="error" x-show="validate?.content_km" x-text="validate?.content_km"></span>
                            </div>
                            <div class="form-row" x-show="locale == arrayLangLocale.en ">
                                <label>@lang('form.body.label.content_en')<span>*</span></label>
                                <textarea id="mytextarea-en" rows="24" class="!h-[500px]" placeholder="@lang('form.body.placeholder.content_en')"
                                    x-model="form.content_en"></textarea>
                                <span class="error" x-show="validate?.content_en" x-text="validate?.content_en"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-button">
                    @can('about-us-update')
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
        Alpine.data('aboutUsPage', () => ({
            form: new FormGroup({
                page: ['about_us', ['required']],
                title_en: [null, ['required']],
                title_km: [null, ['required']],
                content_en: [null, ['required']],
                content_km: [null, ['required']],
                status: ['ACTIVE', ['required']],
            }),
            id: null,
            validate: null,
            loading: false,
            locale: @json(config('dummy.locale.en')),
            async init() {
                feather.replace();
                let data = @json($page);
                if (data) {
                    this.id = data.id;
                    this.form.title_en = data?.title?.en;
                    this.form.title_km = data?.title?.km;
                    this.form.content_en = data?.content?.en;
                    this.form.content_km = data?.content?.km;
                }
                await this.initTinymce();
            },
            async initTinymce() {
                await tinymce.remove();
                await tinymce.init({
                    relative_urls: false,
                    selector: 'textarea#mytextarea-en,textarea#mytextarea-km',
                    height: 500,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'wordcount'
                    ],
                    toolbar: 'fullscreen | bold italic underline | addImage media link | numlist bullist | styles | alignleft aligncenter alignright alignjustify | outdent indent ',
                    setup: function(editor) {
                        editor.ui.registry.addButton('addImage', {
                            text: 'Image',
                            icon: 'image',
                            onAction: () => {
                                fileManager({
                                    multiple: true,
                                    afterClose: (result, basePath) => {
                                        if (result && result.length >
                                            0) {
                                            result.map((file) => {
                                                const img =
                                                    editor.dom
                                                    .createHTML(
                                                        'img', {
                                                            src: basePath +
                                                                file
                                                                .path,
                                                            style: 'width:100% !important;'
                                                        });
                                                editor
                                                    .insertContent(
                                                        img);
                                            });
                                        }
                                    }
                                })
                            }
                        });
                    },
                });
            },

            onSave() {
                this.form.content_km = tinymce.get('mytextarea-km').getContent();
                this.form.content_en = tinymce.get('mytextarea-en').getContent();

                this.$store.confirmDialog.open({
                    data: {
                        title: "@lang('dialog.title')",
                        message: "@lang('dialog.msg.save')",
                        btnClose: "@lang('dialog.button.close')",
                        btnSave: "@lang('dialog.button.save')",
                    },
                    afterClosed: (result) => {
                        if (result) {
                            this.form.disable();
                            this.loading = true;
                            const data = this.form.value();
                            Axios({
                                url: `{{ route('admin-page-about-us-save') }}`,
                                method: 'POST',
                                data: {
                                    ...data,
                                    id: this.id,
                                }
                            }).then((res) => {
                                this.id = res.data.id;
                                if (res.data.error == false) {}
                                Toast({
                                    message: res.data.message,
                                    status: res.data.status,
                                    size: 'small',
                                });
                            }).catch((e) => {
                                this.validate = e.response.data.errors;
                                let validateKhmer = Object.keys(this.validate).filter((
                                    item) => item.includes('_km'));
                                let validateEnglish = Object.keys(this.validate).filter((
                                    item) => item.includes('_en'));

                                if (validateEnglish.length > 0) {
                                    console.log(1, validateEnglish);

                                    this.locale = arrayLangLocale.en;
                                } else {
                                    console.log(2, validateKhmer);

                                    if (validateKhmer.length > 0) {
                                        this.locale = arrayLangLocale.km;
                                    }
                                }
                            }).finally(() => {
                                this.form.enable();
                                this.loading = false;
                            });
                        }
                    }
                });
            }

        }));
    </script>
@endsection
