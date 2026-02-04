@extends('admin::shared.layout')
@section('style')
    <link rel="stylesheet" href="{{ asset('plugin/css/form.css') }}">
@endsection
@section('layout')
    <div class="form-admin" x-data="ourStoryPage">
        @include('admin::shared.header', [
            'title' => __('form.name.our_story'),
            'header_name' => __('form.name.our_story'),
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
                            <div class="form-row">
                                <label>@lang('form.body.label.image') <span>*</span></label>
                                <input type="file" :disabled="form.disabled" accept="image/*" id="image"
                                    class="!p-[12px]" @change="onPreviewImage($el)">
                                <input type="hidden" x-model="form.tmp_file">
                                <template x-if="image_url">
                                    <div
                                        class="h-[250px] rounded-md border border-gray-100 overflow-hidden relative grid place-items-center group mt-2">
                                        <img class="w-full h-full object-contain" :src="image_url" alt="">
                                        <div class="absolute flex gap-2 opacity-0 group-hover:opacity-100 duration-[0.2s]">
                                            <button type="button"
                                                class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                                @click="onViewImage(image_url)">
                                                <span class="material-icons-outlined text-white text-2xl w-[24px]">
                                                    visibility_on
                                                </span>
                                            </button>
                                            <button type="button"
                                                class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                                @click="onRemoveImage()">
                                                <span class="material-icons-outlined text-white text-2xl w-[24px]">
                                                    delete
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <span class="error" x-show="validate?.image" x-text="validate?.image"></span>
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

                                <!-- title_en -->
                                <div class="form-row">
                                    <label>@lang('form.body.label.title_en') <span>*</span></label>
                                    <input type="text" x-model="item.title_en" :disabled="form.disabled"
                                        placeholder="@lang('form.body.placeholder.title_en')" autocomplete="off">
                                    <span class="error" x-show="validate?.[`dataDetail.${index}.title_en`]"
                                        x-text="validate?.[`dataDetail.${index}.title_en`]"></span>
                                </div>

                                <!-- title_km -->
                                <div class="form-row">
                                    <label>@lang('form.body.label.title_km') <span>*</span></label>
                                    <input type="text" x-model="item.title_km" :disabled="form.disabled"
                                        placeholder="@lang('form.body.placeholder.title_km')" autocomplete="off">
                                    <span class="error" x-show="validate?.[`dataDetail.${index}.title_km`]"
                                        x-text="validate?.[`dataDetail.${index}.title_km`]"></span>
                                </div>

                                <!-- description_en -->
                                <div class="form-row">
                                    <label>@lang('form.body.label.description_en') <span>*</span></label>
                                    <textarea rows="2" x-model="item.description_en" placeholder="@lang('form.body.placeholder.description_en')"></textarea>
                                    <span class="error" x-show="validate?.[`dataDetail.${index}.description_en`]"
                                        x-text="validate?.[`dataDetail.${index}.description_en`]"></span>
                                </div>

                                <!-- description_km -->
                                <div class="form-row">
                                    <label>@lang('form.body.label.description_km') <span>*</span></label>
                                    <textarea rows="2" x-model="item.description_km" placeholder="@lang('form.body.placeholder.description_km')"></textarea>
                                    <span class="error" x-show="validate?.[`dataDetail.${index}.description_km`]"
                                        x-text="validate?.[`dataDetail.${index}.description_km`]"></span>
                                </div>
                                <div class="form-row">
                                    <label>@lang('form.body.label.ordering') <span>*</span></label>
                                    <input type="number" x-model="item.ordering" :disabled="form.disabled"
                                        placeholder="@lang('form.body.placeholder.ordering')" autocomplete="off">
                                    <span class="error" x-show="validate?.[`dataDetail.${index}.ordering`]"
                                        x-text="validate?.[`dataDetail.${index}.ordering`]"></span>
                                </div>
                                <!-- icon upload -->
                                <div class="form-row">
                                    <label>@lang('form.body.label.icon') </label>
                                    <input type="file" accept="image/*" class="!p-[12px]" :disabled="form.disabled"
                                        @change="onPreviewIcon($event, index)">
                                    <span class="error" x-show="validate?.[`dataDetail.${index}.icon`]"
                                        x-text="validate?.[`dataDetail.${index}.icon`]"></span>

                                    <!-- preview -->
                                    <template x-if="item.icon_url">
                                        <div
                                            class="h-[130px] rounded-md border border-gray-100 overflow-hidden relative grid place-items-center group mt-2">
                                            <img class="w-full h-full object-contain" :src="item.icon_url" alt="">
                                            <div
                                                class="absolute flex gap-2 opacity-0 group-hover:opacity-100 duration-[0.2s]">
                                                <button type="button"
                                                    class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                                    @click="onViewIcon(item.icon_url)">
                                                    <span
                                                        class="material-icons-outlined text-white text-2xl w-[24px]">visibility_on</span>
                                                </button>
                                                <button type="button"
                                                    class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                                    @click="onRemoveIcon(index)">
                                                    <span
                                                        class="material-icons-outlined text-white text-2xl w-[24px]">delete</span>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </fieldset>
                <div class="form-button">
                    @can('our-story-update')
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
        Alpine.data('ourStoryPage', () => ({
            form: new FormGroup({
                page: ['our_story', ['required']],
                title_en: [null, ['required']],
                title_km: [null, ['required']],
                content_en: [null, ['required']],
                content_km: [null, ['required']],
                image: [null, []],
                tmp_file: [null, []],
                status: ['ACTIVE', ['required']],
            }),
            dataDetail: [{
                title_en: null,
                title_km: null,
                description_en: null,
                description_km: null,
                ordering: null,
                icon: null,
                icon_url: null,
            }],
            id: null,
            validate: null,
            loading: false,
            locale: @json(config('dummy.locale.en')),
            baseUrl: "{{ asset('storage/list-of-value') }}/",
            image_url: null,
            async init() {
                feather.replace();

                let data = @json($page);

                this.dataDetail = [{
                    title_en: null,
                    title_km: null,
                    description_en: null,
                    description_km: null,
                    ordering: null,
                    icon: null,
                    icon_url: null,
                    tmp_icon: null,
                }];

                if (data) {
                    this.id = data.id ?? null;
                    this.form.title_en = data?.title?.en ?? null;
                    this.form.title_km = data?.title?.km ?? null;
                    this.form.content_en = data?.content?.en ?? null;
                    this.form.content_km = data?.content?.km ?? null;
                    this.image_url = data?.image ? this.baseUrl + data.image : null;
                    this.form.tmp_file = data?.image ?? null;

                    const detailList = data?.content?.dataDetail || [];
                    if (detailList.length > 0) {
                        this.dataDetail = detailList.map(item => ({
                            title_en: item.title_en ?? null,
                            title_km: item.title_km ?? null,
                            description_en: item.description_en ?? null,
                            description_km: item.description_km ?? null,
                            ordering: item.ordering ?? null,
                            tmp_icon: item.icon ?? null,
                            icon: null,
                            icon_url: item.icon ?
                                (item.icon.startsWith('http') ? item.icon : this.baseUrl + item
                                    .icon) :
                                null,
                        }));
                    }
                }

                await this.initTinymce();
            },

            onPreviewImage(el) {
                const image = URL.createObjectURL(el.files[0]);
                this.image_url = image;
            },
            onViewImage(path) {
                const thumbnail = Fancybox.show([{
                    src: path,
                    type: "image",
                }, ], {
                    on: {
                        ready: (fancybox) => {
                            document.querySelector('.fancybox__container').style.zIndex = this
                                .$store.libs.getLastIndex() + 1;
                        },
                    }
                });
            },
            onRemoveImage() {
                this.form.tmp_file = null;
                this.image_url = null;
                document.querySelector('#image').value = '';
            },
            addRow() {
                this.dataDetail.push({
                    title_en: null,
                    title_km: null,
                    description_en: null,
                    description_km: null,
                    ordering: null,
                    icon: null,
                    icon_url: null,
                });
            },
            removeRow(index) {
                this.dataDetail.splice(index, 1);
                if (this.dataDetail.length == 1) {
                    this.dataDetail[0].amount = null;
                }
            },
            onPreviewIcon(event, index) {
                const file = event.target.files[0];
                if (!file) return;
                this.dataDetail[index].icon = file;
                this.dataDetail[index].icon_url = URL.createObjectURL(file);
            },

            onViewIcon(path) {
                const thumbnail = Fancybox.show([{
                    src: path,
                    type: "image",
                }, ], {
                    on: {
                        ready: (fancybox) => {
                            document.querySelector('.fancybox__container').style.zIndex = this
                                .$store.libs.getLastIndex() + 1;
                        },
                    }
                });
            },
            onRemoveIcon(index) {
                this.dataDetail[index].icon = null;
                this.dataDetail[index].icon_url = null;
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

                            const formData = new FormData();
                            const formValue = this.form.value();

                            formData.append('id', this.id ?? '');

                            for (const key in formValue) {
                                formData.append(key, formValue[key]);
                            }

                            const file = document.querySelector('#image');
                            if (file?.files?.length) {
                                formData.append('image', file.files[0]);
                            }

                            this.dataDetail.forEach((item, index) => {
                                formData.append(`dataDetail[${index}][title_en]`, item
                                    .title_en ?? '');
                                formData.append(`dataDetail[${index}][title_km]`, item
                                    .title_km ?? '');
                                formData.append(`dataDetail[${index}][description_en]`, item
                                    .description_en ?? '');
                                formData.append(`dataDetail[${index}][description_km]`, item
                                    .description_km ?? '');
                                formData.append(`dataDetail[${index}][ordering]`, item
                                    .ordering ?? '');
                                if (item.icon instanceof File) {
                                    formData.append(`dataDetail[${index}][icon]`, item
                                        .icon);
                                }
                                if (item.tmp_icon) {
                                    formData.append(`dataDetail[${index}][tmp_icon]`, item
                                        .tmp_icon);
                                }
                            });

                            Axios({
                                url: `{{ route('admin-page-our-story-save') }}`,
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'multipart/form-data',
                                },
                                data: formData,
                            }).then((res) => {
                                this.id = res.data.id;
                                Toast({
                                    message: res.data.message,
                                    status: res.data.status,
                                    size: 'small',
                                });
                            }).catch((e) => {
                                this.validate = e.response.data.errors;
                                let validateKhmer = Object.keys(this.validate).filter(
                                    item => item.includes('_km'));
                                let validateEnglish = Object.keys(this.validate).filter(
                                    item => item.includes('_en'));

                                if (validateEnglish.length > 0) {
                                    this.locale = arrayLangLocale.en;
                                } else if (validateKhmer.length > 0) {
                                    this.locale = arrayLangLocale.km;
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
