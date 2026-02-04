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
    <div class="form-admin" x-data="contactUsPage">
        @include('admin::shared.header', [
            'title' => __('form.name.contact_us'),
            'header_name' => __('form.name.contact_us'),
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
                <div class="row">
                    <div class="form-row">
                        <label>@lang('form.body.label.location')</label>
                        <div class="map relative !w-full h-[200px]" @click="onAddMap()">
                            <iframe x-bind:src="form.embed_map" width="100%" height="100%" style="border:0;"
                                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.494548728989!2d106.6299953147693!3d10.768500992316402!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f0f0b5b0a0b%3A0x7e1b2b2b2b2b2b2b!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBQaMaw4budbmcgVGjhu6cgQ-G7kQ!5e0!3m2!1svi!2s!4v1625581000000!5m2!1svi!2s">
                            </iframe>
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
                                            <img class="w-full h-full object-contain" :src="item.icon_url"
                                                alt="">
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
                    @can('contact-us-update')
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
        Alpine.data('contactUsPage', () => ({
            form: new FormGroup({
                page: ['contact_us', ['required']],
                title_en: [null, ['required']],
                title_km: [null, ['required']],
                short_detail_en: [null, ['required']],
                short_detail_km: [null, ['required']],
                embed_map: [null, ['required']],
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
            baseUrl: "{{ asset('storage/list-of-value') }}/",
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
                    this.form.embed_map = data?.content?.embed_map;

                    this.dataDetail = (data?.content?.dataDetail || []).map(item => ({
                        ...item,
                        tmp_icon: item.icon || null,
                        icon: null,
                        icon_url: item.icon ? (item.icon.startsWith('http') ? item.icon : this
                            .baseUrl + item.icon) : null,
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
                                formData.append(`dataDetail[${index}][icon]`, item.icon);
                            }
                            if (item.tmp_icon) {
                                formData.append(`dataDetail[${index}][tmp_icon]`, item
                                    .tmp_icon);
                            }
                        });

                        Axios.post(`{{ route('admin-page-contact-us-save') }}`, formData, {
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
