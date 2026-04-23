@extends('admin::shared.layout')
@section('style')
    <link rel="stylesheet" href="{{ asset('plugin/css/form.css') }}">
    <style>
        .map {
            position: relative;
            width: 100%;
            height: 300px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #d8dce5;
        }

        .map::before {
            content: "Click to update Google Embed map";
            color: #fff;
            display: grid;
            place-items: center;
            position: absolute;
            inset: 0;
            z-index: 1;
            background-color: rgba(51, 51, 51, 0.78);
            width: 100%;
            height: 100%;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
        }

        .map:hover::before {
            opacity: 1;
        }

        .map iframe {
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
                <fieldset class="border-[#d8dce5] border rounded p-3 mb-3">
                    <legend>@lang('form.name.find_us')</legend>
                    <div class="row-2">
                        <div class="form-row">
                            <label>@lang('form.body.label.title_en')<span>*</span></label>
                            <input type="text" placeholder="@lang('form.body.placeholder.title_en')" min="8"
                                id="title_en" x-model="form.title_en" autocomplete="off">
                            <span class="error" x-show="validate?.title_en" x-text="validate?.title_en"></span>
                        </div>
                        <div class="form-row">
                            <label>@lang('form.body.label.title_km')<span>*</span></label>
                            <input type="text" placeholder="@lang('form.body.placeholder.title_km')" min="8"
                                id="title_km" x-model="form.title_km" autocomplete="off">
                            <span class="error" x-show="validate?.title_km" x-text="validate?.title_km"></span>
                        </div>
                    </div>
                    <div class="row-2">
                        <div class="form-row">
                            <label>@lang('form.body.label.description_en')<span>*</span> </label>
                            <textarea x-model="form.short_detail_en" rows="1" placeholder="@lang('form.body.placeholder.description_en')"></textarea>
                            <span class="error" x-show="validate?.short_detail_en"
                                x-text="validate?.short_detail_en"></span>
                        </div>
                        <div class="form-row">
                            <label>@lang('form.body.label.description_km')<span>*</span> </label>
                            <textarea x-model="form.short_detail_km" rows="1" placeholder="@lang('form.body.placeholder.description_km')"></textarea>
                            <span class="error" x-show="validate?.short_detail_km"
                                x-text="validate?.short_detail_km"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-row">
                            <label>@lang('form.body.label.location')<span>*</span></label>
                            <div class="map !w-full h-[260px]" @click="onAddMap()">
                                <iframe x-bind:src="form.embed_map || 'about:blank'" width="100%" height="100%"
                                    style="border:0;" allowfullscreen="" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                            <span class="error" x-show="validate?.embed_map" x-text="validate?.embed_map"></span>
                        </div>
                    </div>
                </fieldset>

                <div class="form-button mb-3">
                    @can('contact-us-update')
                        <button type="button" @click="onSave()" :disabled="form.disabled || loading" color="primary"
                            class="!rounded-[50px]">
                            <span class="material-icons mr-1">save</span>
                            <span>Save</span>
                            <div class="loader" style="display: none" x-show="loading"></div>
                        </button>
                    @endcan
                </div>

                <fieldset class="border-[#d8dce5] border rounded p-3 mb-3">
                    <legend>@lang('form.name.contact_info')</legend>
                    <div class="form-button mb-3">
                        @can('contact-us-update')
                            <button type="button" color="primary" class="!rounded-[50px]"
                                @click="openCreateDetailDialog()">
                                <span class="material-icons mr-1">add</span>
                                <span>@lang('form.name.create')</span>
                            </button>
                        @endcan
                    </div>

                    <div class="border border-gray-200 rounded-md overflow-hidden">
                        <table class="w-full border-collapse">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left text-sm text-gray-600" style="width: 60px; padding: 12px;">
                                        @lang('table.field.no')
                                    </th>
                                    <th class="text-left text-sm text-gray-600" style="width: 100px; padding: 12px;">
                                        @lang('form.body.label.icon')
                                    </th>
                                    <th class="text-left text-sm text-gray-600" style="padding: 12px;">
                                        @lang('table.field.title_en')
                                    </th>
                                    <th class="text-left text-sm text-gray-600" style="padding: 12px;">
                                        @lang('form.body.label.title_km')
                                    </th>
                                    <th class="text-left text-sm text-gray-600" style="padding: 12px;">
                                        @lang('table.field.description')
                                    </th>
                                    <th class="text-left text-sm text-gray-600" style="width: 110px; padding: 12px;">
                                        @lang('table.field.ordering')
                                    </th>
                                    <th class="text-center text-sm text-gray-600" style="width: 100px; padding: 12px;">
                                        @lang('table.field.action')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="dataDetail.length === 0">
                                    <tr>
                                        <td colspan="7" class="text-center text-sm text-gray-400"
                                            style="padding: 28px;">
                                            @lang('dialog.empty.title')
                                        </td>
                                    </tr>
                                </template>
                                <template x-for="(item, index) in dataDetail" :key="index">
                                    <tr class="border-t border-gray-200">
                                        <td class="text-sm text-gray-600" style="padding: 12px;" x-text="index + 1"></td>
                                        <td style="padding: 12px;">
                                            <template x-if="item.icon_url">
                                                <button type="button" class="h-[50px] w-[50px] rounded-md overflow-hidden"
                                                    @click="onViewIcon(item.icon_url)">
                                                    <img class="w-full h-full object-contain" :src="item.icon_url"
                                                        alt="">
                                                </button>
                                            </template>
                                            <template x-if="!item.icon_url">
                                                <span class="text-sm text-gray-400">-</span>
                                            </template>
                                        </td>
                                        <td class="text-sm text-gray-600" style="padding: 12px;"
                                            x-text="item.title_en || '-'"></td>
                                        <td class="text-sm text-gray-600" style="padding: 12px;"
                                            x-text="item.title_km || '-'"></td>
                                        <td class="text-sm text-gray-600" style="padding: 12px;">
                                            <span x-text="item.description_en || '-'"></span>
                                        </td>
                                        <td class="text-sm text-gray-600" style="padding: 12px;"
                                            x-text="item.ordering || '-'"></td>
                                        <td style="padding: 12px;">
                                            @can('contact-us-update')
                                                <div class="flex justify-center gap-2">
                                                    <button type="button"
                                                        class="h-[35px] w-[35px] rounded-md border border-gray-200 grid place-items-center"
                                                        @click="openEditDetailDialog(index)">
                                                        <span class="material-icons text-blue-500">edit</span>
                                                    </button>
                                                    <button type="button"
                                                        class="h-[35px] w-[35px] rounded-md border border-gray-200 grid place-items-center"
                                                        @click="removeDetail(index)">
                                                        <span class="material-icons text-red-500">delete</span>
                                                    </button>
                                                </div>
                                            @endcan
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </fieldset>
            </div>
            <div class="form-footer"></div>
        </form>

        <div x-show="detailDialogOpen" x-transition.opacity
            style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(17, 24, 39, 0.45); padding: 56px 16px 24px; overflow-y: auto;"
            @click.self="closeDetailDialog()" @keydown.escape.window="closeDetailDialog()">
            <div class="form-wrapper"
                style="width: min(820px, 95vw); margin: 0 auto; padding: 0; background: #fff; border-radius: 8px; box-shadow: 0 20px 45px rgba(15, 23, 42, 0.22); overflow: hidden; height: auto; min-height: 0;">
                <div class="form-header"
                    style="padding: 14px 20px; border-bottom: 1px solid #edf0f5; align-items: center;">
                    <h3 x-show="detailEditIndex === null" style="font-size: 16px;">@lang('form.name.create')
                        (@lang('form.name.contact_info'))</h3>
                    <h3 x-show="detailEditIndex !== null" style="font-size: 16px;">@lang('form.header.update', ['name' => __('form.name.contact_info')])</h3>
                    <span style="cursor: pointer;" @click="closeDetailDialog()"><i data-feather="x"></i></span>
                </div>
                <div class="form-body overflow-y-auto" style="max-height: 64vh; padding: 18px 20px 8px;">
                    <div class="row-2">
                        <div class="form-row">
                            <label>@lang('form.body.label.title_en') <span>*</span></label>
                            <input type="text" x-model="detailForm.title_en"
                                placeholder="@lang('form.body.placeholder.title_en')" autocomplete="off">
                            <span class="error" x-show="detailValidate?.title_en" x-text="detailValidate?.title_en"></span>
                        </div>
                        <div class="form-row">
                            <label>@lang('form.body.label.title_km') <span>*</span></label>
                            <input type="text" x-model="detailForm.title_km"
                                placeholder="@lang('form.body.placeholder.title_km')" autocomplete="off">
                            <span class="error" x-show="detailValidate?.title_km" x-text="detailValidate?.title_km"></span>
                        </div>
                    </div>
                    <div class="row-2">
                        <div class="form-row">
                            <label>@lang('form.body.label.description_en') <span>*</span></label>
                            <textarea rows="3" x-model="detailForm.description_en" placeholder="@lang('form.body.placeholder.description_en')"></textarea>
                            <span class="error" x-show="detailValidate?.description_en"
                                x-text="detailValidate?.description_en"></span>
                        </div>
                        <div class="form-row">
                            <label>@lang('form.body.label.description_km') <span>*</span></label>
                            <textarea rows="3" x-model="detailForm.description_km" placeholder="@lang('form.body.placeholder.description_km')"></textarea>
                            <span class="error" x-show="detailValidate?.description_km"
                                x-text="detailValidate?.description_km"></span>
                        </div>
                    </div>
                    <div class="row-2">
                        <div class="form-row">
                            <label>@lang('form.body.label.ordering') <span>*</span></label>
                            <input type="number" x-model="detailForm.ordering"
                                placeholder="@lang('form.body.placeholder.ordering')" autocomplete="off">
                            <span class="error" x-show="detailValidate?.ordering" x-text="detailValidate?.ordering"></span>
                        </div>
                        <div class="form-row">
                            <label>@lang('form.body.label.icon')</label>
                            <input type="file" accept="image/*" class="!p-[12px]" x-ref="detailIconInput"
                                @change="onPreviewDetailIcon($event)">
                            <span class="error" x-show="detailValidate?.icon" x-text="detailValidate?.icon"></span>
                            <template x-if="detailForm.icon_url">
                                <div
                                    class="h-[110px] rounded-md border border-gray-100 overflow-hidden relative grid place-items-center group mt-2">
                                    <img class="w-full h-full object-contain" :src="detailForm.icon_url" alt="">
                                    <div class="absolute flex gap-2 opacity-0 group-hover:opacity-100 duration-[0.2s]">
                                        <button type="button"
                                            class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                            @click="onViewIcon(detailForm.icon_url)">
                                            <span class="material-icons-outlined text-white text-2xl w-[24px]">
                                                visibility_on
                                            </span>
                                        </button>
                                        <button type="button"
                                            class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                            @click="onRemoveDetailIcon()">
                                            <span class="material-icons-outlined text-white text-2xl w-[24px]">
                                                delete
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="form-footer"
                    style="height: auto; padding: 12px 20px; border-top: 1px solid #edf0f5; background: #f9fafb;">
                    <div class="form-button" style="padding-top: 0;">
                        <button type="button" @click="closeDetailDialog()" :disabled="detailLoading">
                            <span>@lang('dialog.button.close')</span>
                        </button>
                        <button type="button" color="primary" @click="onSaveDetail()" :disabled="detailLoading">
                            <span class="material-icons mr-1">save</span>
                            <span>@lang('form.button.save')</span>
                            <div class="loader" style="display: none" x-show="detailLoading"></div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
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
            dataDetail: [],
            detailDialogOpen: false,
            detailEditIndex: null,
            detailValidate: null,
            detailForm: {},
            detailLoading: false,
            id: null,
            validate: null,
            loading: false,
            baseUrl: "{{ asset('storage/list-of-value') }}/",
            async init() {
                feather.replace();
                this.detailForm = this.emptyDetail();

                let data = @json($page);
                if (data) {
                    this.id = data.id;
                    this.form.page = data?.page;
                    this.form.title_en = data?.title?.en;
                    this.form.title_km = data?.title?.km;
                    this.form.short_detail_en = data?.short_detail?.en;
                    this.form.short_detail_km = data?.short_detail?.km;
                    this.form.embed_map = data?.content?.embed_map;

                    const detailList = data?.content?.dataDetail || [];
                    this.dataDetail = detailList.map(item => this.normalizeDetail(item));
                }
            },
            applySavedPage(data) {
                if (!data) return;

                this.id = data.id ?? this.id;
                this.form.page = data?.page ?? this.form.page;
                this.form.title_en = data?.title?.en ?? this.form.title_en;
                this.form.title_km = data?.title?.km ?? this.form.title_km;
                this.form.short_detail_en = data?.short_detail?.en ?? this.form.short_detail_en;
                this.form.short_detail_km = data?.short_detail?.km ?? this.form.short_detail_km;
                this.form.embed_map = data?.content?.embed_map ?? this.form.embed_map;

                const detailList = data?.content?.dataDetail || [];
                this.dataDetail = detailList.map(item => this.normalizeDetail(item));
            },
            emptyDetail() {
                return {
                    title_en: null,
                    title_km: null,
                    description_en: null,
                    description_km: null,
                    ordering: null,
                    icon: null,
                    icon_url: null,
                    tmp_icon: null,
                };
            },
            normalizeDetail(item = {}) {
                const icon = item.icon || item.tmp_icon || null;

                return {
                    title_en: item.title_en ?? null,
                    title_km: item.title_km ?? null,
                    description_en: item.description_en ?? null,
                    description_km: item.description_km ?? null,
                    ordering: item.ordering ?? null,
                    icon: item.icon instanceof File ? item.icon : null,
                    tmp_icon: icon instanceof File ? null : icon,
                    icon_url: item.icon_url || this.resolveFileUrl(icon),
                };
            },
            resolveFileUrl(file) {
                if (!file) return null;
                if (file instanceof File) return URL.createObjectURL(file);
                if (file.startsWith('http') || file.startsWith('blob:')) return file;

                return this.baseUrl + file;
            },
            cloneDetail(item) {
                return {
                    ...item
                };
            },
            getNextOrdering() {
                return this.dataDetail.reduce((max, item) => {
                    return Math.max(max, Number(item.ordering) || 0);
                }, 0) + 1;
            },
            resetDetailFileInputs() {
                this.$nextTick(() => {
                    if (this.$refs.detailIconInput) {
                        this.$refs.detailIconInput.value = '';
                    }
                    feather.replace();
                });
            },
            openCreateDetailDialog() {
                this.detailEditIndex = null;
                this.detailValidate = null;
                this.detailForm = {
                    ...this.emptyDetail(),
                    ordering: this.getNextOrdering(),
                };
                this.detailDialogOpen = true;
                this.resetDetailFileInputs();
            },
            openEditDetailDialog(index) {
                this.detailEditIndex = index;
                this.detailValidate = null;
                this.detailForm = this.cloneDetail(this.dataDetail[index]);
                this.detailDialogOpen = true;
                this.resetDetailFileInputs();
            },
            closeDetailDialog() {
                if (this.detailLoading) return;
                this.detailDialogOpen = false;
                this.detailValidate = null;
            },
            validateDetailForm() {
                const required = 'This field is required.';
                const errors = {};

                if (!this.detailForm.title_en) errors.title_en = required;
                if (!this.detailForm.title_km) errors.title_km = required;
                if (!this.detailForm.description_en) errors.description_en = required;
                if (!this.detailForm.description_km) errors.description_km = required;
                if (this.detailForm.ordering === null || this.detailForm.ordering === '') {
                    errors.ordering = required;
                }

                this.detailValidate = errors;
                return Object.keys(errors).length === 0;
            },
            async onSaveDetail() {
                if (this.detailLoading || !this.validateDetailForm()) return;

                const originalDataDetail = this.dataDetail.map(item => this.cloneDetail(item));
                const detail = this.normalizeDetail(this.detailForm);
                let detailIndex = this.detailEditIndex;

                if (detailIndex === null) {
                    this.dataDetail.push(detail);
                    detailIndex = this.dataDetail.length - 1;
                } else {
                    this.dataDetail.splice(detailIndex, 1, detail);
                }

                const saved = await this.submitContactUs(true, detailIndex);

                if (saved) {
                    this.detailDialogOpen = false;
                    this.detailValidate = null;
                } else {
                    this.dataDetail = originalDataDetail;
                }
            },
            removeDetail(index) {
                this.$store.confirmDialog.open({
                    data: {
                        title: "@lang('dialog.title')",
                        message: "@lang('dialog.msg.delete')",
                        btnClose: "@lang('dialog.button.close')",
                        btnSave: "@lang('dialog.button.delete')",
                    },
                    afterClosed: (result) => {
                        if (!result) return;
                        this.dataDetail.splice(index, 1);
                    }
                });
            },
            onPreviewDetailIcon(event) {
                const file = event.target.files[0];
                if (!file) return;
                this.detailForm.icon = file;
                this.detailForm.tmp_icon = null;
                this.detailForm.icon_url = URL.createObjectURL(file);
            },
            onRemoveDetailIcon() {
                this.detailForm.icon = null;
                this.detailForm.icon_url = null;
                this.detailForm.tmp_icon = null;
                if (this.$refs.detailIconInput) {
                    this.$refs.detailIconInput.value = '';
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
            onViewIcon(path) {
                Fancybox.show([{
                    src: path,
                    type: "image",
                }, ], {
                    on: {
                        ready: () => {
                            document.querySelector('.fancybox__container').style.zIndex = this
                                .$store.libs.getLastIndex() + 1;
                        },
                    }
                });
            },
            buildFormData() {
                const formData = new FormData();
                const formValue = this.form.value();

                for (const key in formValue) {
                    formData.append(key, formValue[key]);
                }

                if (this.id !== null && this.id !== 'null') {
                    formData.append('id', this.id);
                }

                this.dataDetail.forEach((item, index) => {
                    formData.append(`dataDetail[${index}][title_en]`, item.title_en ?? '');
                    formData.append(`dataDetail[${index}][title_km]`, item.title_km ?? '');
                    formData.append(`dataDetail[${index}][description_en]`, item.description_en ?? '');
                    formData.append(`dataDetail[${index}][description_km]`, item.description_km ?? '');
                    formData.append(`dataDetail[${index}][ordering]`, item.ordering ?? '');
                    if (item.icon instanceof File) {
                        formData.append(`dataDetail[${index}][icon]`, item.icon);
                    }
                    if (item.tmp_icon) {
                        formData.append(`dataDetail[${index}][tmp_icon]`, item.tmp_icon);
                    }
                });

                return formData;
            },
            getDetailServerErrors(errors, index) {
                if (index === null || !errors) return {};

                return ['title_en', 'title_km', 'description_en', 'description_km', 'ordering', 'icon']
                    .reduce((carry, field) => {
                        const key = `dataDetail.${index}.${field}`;
                        if (errors[key]) {
                            carry[field] = Array.isArray(errors[key]) ? errors[key][0] : errors[key];
                        }

                        return carry;
                    }, {});
            },
            async submitContactUs(useDetailLoading = false, detailIndex = null) {
                if (useDetailLoading) {
                    this.detailLoading = true;
                } else {
                    this.form.disable();
                    this.loading = true;
                }

                try {
                    const res = await Axios.post(`{{ route('admin-page-contact-us-save') }}`, this.buildFormData(), {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });

                    this.id = res.data.id;
                    this.validate = null;
                    this.applySavedPage(res.data.data);

                    Toast({
                        message: res.data.message,
                        status: res.data.status,
                        size: 'small',
                    });

                    return true;
                } catch (e) {
                    const errors = e.response?.data?.errors;
                    this.validate = errors;

                    if (useDetailLoading) {
                        const detailErrors = this.getDetailServerErrors(errors, detailIndex);
                        if (Object.keys(detailErrors).length > 0) {
                            this.detailValidate = detailErrors;
                        }
                    }

                    return false;
                } finally {
                    if (useDetailLoading) {
                        this.detailLoading = false;
                    } else {
                        this.form.enable();
                        this.loading = false;
                    }
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

                        await this.submitContactUs();
                    }
                });
            }
        }));
    </script>
@endsection
