@extends('admin::shared.layout')
@section('style')
    <link rel="stylesheet" href="{{ asset('plugin/css/form.css') }}">
@endsection
@section('layout')
    <div class="form-admin" x-data="OurProgram">
        @include('admin::shared.header', [
            'title' => __('form.name.our_program'),
            'header_name' => __('form.name.our_program'),
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
                                <div class="row-3" style="grid-column: 1 / -1;">
                                    <div class="form-row">
                                        <label>@lang('form.body.label.ordering') <span>*</span></label>
                                        <input type="number" x-model="item.ordering" :disabled="form.disabled"
                                            placeholder="@lang('form.body.placeholder.ordering')" autocomplete="off">
                                        <span class="error" x-show="validate?.[`dataDetail.${index}.ordering`]"
                                            x-text="validate?.[`dataDetail.${index}.ordering`]"></span>
                                    </div>
                                    <!-- image upload -->
                                    <div class="form-row">
                                        <label>@lang('form.body.label.image') </label>
                                        <input type="file" accept="image/*" class="!p-[12px]" :disabled="form.disabled"
                                            @change="onPreviewImage($event, index)">
                                        <span class="error" x-show="validate?.[`dataDetail.${index}.image`]"
                                            x-text="validate?.[`dataDetail.${index}.image`]"></span>

                                        <!-- preview -->
                                        <template x-if="item.image_url">
                                            <div
                                                class="h-[130px] rounded-md border border-gray-100 overflow-hidden relative grid place-items-center group mt-2">
                                                <img class="w-full h-full object-contain" :src="item.image_url"
                                                    alt="">
                                                <div
                                                    class="absolute flex gap-2 opacity-0 group-hover:opacity-100 duration-[0.2s]">
                                                    <button type="button"
                                                        class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                                        @click="onViewImage(item.image_url)">
                                                        <span
                                                            class="material-icons-outlined text-white text-2xl w-[24px]">visibility_on</span>
                                                    </button>
                                                    <button type="button"
                                                        class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                                        @click="onRemoveImage(index)">
                                                        <span
                                                            class="material-icons-outlined text-white text-2xl w-[24px]">delete</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
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
                            </div>
                        </template>
                    </div>
                </fieldset>

                <div class="form-button">
                    @can('our-program-update')
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
        Alpine.data('OurProgram', () => ({
            form: new FormGroup({
                page: ['our_program', ['required']],
                title_en: [null, ['required']],
                title_km: [null, ['required']],
                short_detail_en: [null, ['required']],
                short_detail_km: [null, ['required']],
                status: ['ACTIVE', ['required']],
            }),
            dataDetail: [{
                title_en: null,
                title_km: null,
                description_en: null,
                description_km: null,
                ordering: null,
                image: null,
                image_url: null,
                tmp_image: null,
                icon: null,
                icon_url: null,
                tmp_icon: null,
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

                    const detailList = data?.content?.dataDetail || [];
                    if (detailList.length > 0) {
                        this.dataDetail = detailList.map(item => ({
                            ...item,
                            tmp_image: item.image || null,
                            tmp_icon: item.icon || null,
                            image: null,
                            icon: null,
                            image_url: item.image ? (item.image.startsWith('http') ? item.image : this
                                .baseUrl + item.image) : null,
                            icon_url: item.icon ? (item.icon.startsWith('http') ? item.icon : this
                                .baseUrl + item.icon) : null,
                        }));
                    }

                }
            },
            addRow() {
                this.dataDetail.push({
                    title_en: null,
                    title_km: null,
                    description_en: null,
                    description_km: null,
                    ordering: null,
                    image: null,
                    image_url: null,
                    tmp_image: null,
                    icon: null,
                    icon_url: null,
                    tmp_icon: null,
                });
            },
            removeRow(index) {
                this.dataDetail.splice(index, 1);
                if (this.dataDetail.length == 1) {
                    this.dataDetail[0].amount = null;
                }
            },
            onPreviewImage(event, index) {
                const file = event.target.files[0];
                if (!file) return;
                this.dataDetail[index].image = file;
                this.dataDetail[index].image_url = URL.createObjectURL(file);
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
            onRemoveImage(index) {
                this.dataDetail[index].image = null;
                this.dataDetail[index].image_url = null;
                this.dataDetail[index].tmp_image = null;
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
                this.dataDetail[index].tmp_icon = null;
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
                            if (item.image instanceof File) {
                                formData.append(`dataDetail[${index}][image]`, item.image);
                            }
                            if (item.tmp_image) {
                                formData.append(`dataDetail[${index}][tmp_image]`, item
                                    .tmp_image);
                            }
                            if (item.icon instanceof File) {
                                formData.append(`dataDetail[${index}][icon]`, item.icon);
                            }
                            if (item.tmp_icon) {
                                formData.append(`dataDetail[${index}][tmp_icon]`, item
                                    .tmp_icon);
                            }
                        });

                        Axios.post(`{{ route('admin-page-our-program-save') }}`, formData, {
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
