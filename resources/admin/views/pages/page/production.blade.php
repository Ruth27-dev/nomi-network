@extends('admin::shared.layout')
@section('style')
    <link rel="stylesheet" href="{{ asset('plugin/css/form.css') }}">
@endsection
@section('layout')
    <div class="form-admin" x-data="ProductionPage">
        @include('admin::shared.header', [
            'title' => __('form.name.production'),
            'header_name' => __('form.name.production'),
        ])
        <form id="form" class="form-wrapper">
            <div class="form-header"></div>
            <div class="form-body">
                <fieldset class="border-[#d8dce5] border rounded p-3 mb-3">
                    <legend>Detail</legend>
                    <div class="form-button mb-3">
                        @can('production-update')
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
                                    <th class="text-left text-sm text-gray-600" style="padding: 12px;">
                                        @lang('form.body.label.description_en')
                                    </th>
                                    <th class="text-left text-sm text-gray-600" style="padding: 12px;">
                                        @lang('form.body.label.description_km')
                                    </th>
                                    <th class="text-left text-sm text-gray-600" style="width: 110px; padding: 12px;">
                                        @lang('table.field.ordering')
                                    </th>
                                    <th class="text-left text-sm text-gray-600" style="width: 100px; padding: 12px;">
                                        @lang('form.body.label.image')
                                    </th>
                                    <th class="text-center text-sm text-gray-600" style="width: 100px; padding: 12px;">
                                        @lang('table.field.action')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="dataDetail.length === 0">
                                    <tr>
                                        <td colspan="6" class="text-center text-sm text-gray-400"
                                            style="padding: 28px;">
                                            @lang('dialog.empty.title')
                                        </td>
                                    </tr>
                                </template>
                                <template x-for="(item, index) in dataDetail" :key="index">
                                    <tr class="border-t border-gray-200">
                                        <td class="text-sm text-gray-600" style="padding: 12px;" x-text="index + 1"></td>
                                        <td class="text-sm text-gray-600" style="padding: 12px;"
                                            x-text="stripHtml(item.description_en) || '-'"></td>
                                        <td class="text-sm text-gray-600" style="padding: 12px;"
                                            x-text="stripHtml(item.description_km) || '-'"></td>
                                        <td class="text-sm text-gray-600" style="padding: 12px;"
                                            x-text="item.ordering || '-'"></td>
                                        <td style="padding: 12px;">
                                            <template x-if="item.image_url">
                                                <button type="button" class="h-[50px] w-[50px] rounded-md overflow-hidden"
                                                    @click="onViewImage(item.image_url)">
                                                    <img class="w-full h-full object-contain" :src="item.image_url"
                                                        alt="">
                                                </button>
                                            </template>
                                            <template x-if="!item.image_url">
                                                <span class="text-sm text-gray-400">-</span>
                                            </template>
                                        </td>
                                        <td style="padding: 12px;">
                                            @can('production-update')
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

                <fieldset class="border-[#d8dce5] border rounded p-3 mb-3">
                    <legend>Image Slider</legend>
                    <div class="form-button mb-3">
                        @can('production-update')
                            <button type="button" color="primary" class="!rounded-[50px]"
                                @click="openCreateSlideDialog()">
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
                                    <th class="text-left text-sm text-gray-600" style="width: 130px; padding: 12px;">
                                        @lang('form.body.label.image')
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
                                <template x-if="imageSlides.length === 0">
                                    <tr>
                                        <td colspan="4" class="text-center text-sm text-gray-400"
                                            style="padding: 28px;">
                                            @lang('dialog.empty.title')
                                        </td>
                                    </tr>
                                </template>
                                <template x-for="(item, index) in imageSlides" :key="index">
                                    <tr class="border-t border-gray-200">
                                        <td class="text-sm text-gray-600" style="padding: 12px;" x-text="index + 1"></td>
                                        <td style="padding: 12px;">
                                            <template x-if="item.image_url">
                                                <button type="button" class="h-[50px] w-[50px] rounded-md overflow-hidden"
                                                    @click="onViewImage(item.image_url)">
                                                    <img class="w-full h-full object-contain" :src="item.image_url"
                                                        alt="">
                                                </button>
                                            </template>
                                            <template x-if="!item.image_url">
                                                <span class="text-sm text-gray-400">-</span>
                                            </template>
                                        </td>
                                        <td class="text-sm text-gray-600" style="padding: 12px;"
                                            x-text="item.ordering || '-'"></td>
                                        <td style="padding: 12px;">
                                            @can('production-update')
                                                <div class="flex justify-center gap-2">
                                                    <button type="button"
                                                        class="h-[35px] w-[35px] rounded-md border border-gray-200 grid place-items-center"
                                                        @click="openEditSlideDialog(index)">
                                                        <span class="material-icons text-blue-500">edit</span>
                                                    </button>
                                                    <button type="button"
                                                        class="h-[35px] w-[35px] rounded-md border border-gray-200 grid place-items-center"
                                                        @click="removeSlide(index)">
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
                        (Detail)</h3>
                    <h3 x-show="detailEditIndex !== null" style="font-size: 16px;">@lang('form.header.update', ['name' => 'Detail'])</h3>
                    <span style="cursor: pointer;" @click="closeDetailDialog()"><i data-feather="x"></i></span>
                </div>
                <div class="form-body overflow-y-auto" style="max-height: 64vh; padding: 18px 20px 8px;">
                    <div class="form-header mb-0 !text-sm !flex !justify-end">
                        <div class="change-language">
                            <div class="change-language-row">
                                <div class="change-language-row-item" :class="{ 'active': detailLocale == arrayLangLocale.en }" @click="detailLocale = arrayLangLocale.en">
                                    <span>@lang('form.locale.en')</span>
                                </div>
                                <div class="change-language-row-item" :class="{ 'active': detailLocale == arrayLangLocale.km }" @click="detailLocale = arrayLangLocale.km">
                                    <span>@lang('form.locale.km')</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row" x-show="detailLocale == arrayLangLocale.en">
                        <label>@lang('form.body.label.description_en') <span>*</span></label>
                        <textarea id="prod-detail-en" rows="3" x-model="detailForm.description_en" placeholder="@lang('form.body.placeholder.description_en')"></textarea>
                        <span class="error" x-show="detailValidate?.description_en"
                            x-text="detailValidate?.description_en"></span>
                    </div>
                    <div class="form-row" x-show="detailLocale == arrayLangLocale.km">
                        <label>@lang('form.body.label.description_km') <span>*</span></label>
                        <textarea id="prod-detail-km" rows="3" x-model="detailForm.description_km" placeholder="@lang('form.body.placeholder.description_km')"></textarea>
                        <span class="error" x-show="detailValidate?.description_km"
                            x-text="detailValidate?.description_km"></span>
                    </div>
                    <div class="row-2">
                        <div class="form-row">
                            <label>@lang('form.body.label.ordering') <span>*</span></label>
                            <input type="number" x-model="detailForm.ordering"
                                placeholder="@lang('form.body.placeholder.ordering')" autocomplete="off">
                            <span class="error" x-show="detailValidate?.ordering" x-text="detailValidate?.ordering"></span>
                        </div>
                        <div class="form-row">
                            <label>@lang('form.body.label.image')</label>
                            <input type="file" accept="image/*" class="!p-[12px]" x-ref="detailImageInput"
                                @change="onPreviewDetailImage($event)">
                            <template x-if="detailForm.image_url">
                                <div
                                    class="h-[110px] rounded-md border border-gray-100 overflow-hidden relative grid place-items-center group mt-2">
                                    <img class="w-full h-full object-contain" :src="detailForm.image_url" alt="">
                                    <div class="absolute flex gap-2 opacity-0 group-hover:opacity-100 duration-[0.2s]">
                                        <button type="button"
                                            class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                            @click="onViewImage(detailForm.image_url)">
                                            <span class="material-icons-outlined text-white text-2xl w-[24px]">
                                                visibility_on
                                            </span>
                                        </button>
                                        <button type="button"
                                            class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                            @click="onRemoveDetailImage()">
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
                        <button type="button" @click="closeDetailDialog()">
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

        <div x-show="slideDialogOpen" x-transition.opacity
            style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(17, 24, 39, 0.45); padding: 56px 16px 24px; overflow-y: auto;"
            @click.self="closeSlideDialog()" @keydown.escape.window="closeSlideDialog()">
            <div class="form-wrapper"
                style="width: min(620px, 95vw); margin: 0 auto; padding: 0; background: #fff; border-radius: 8px; box-shadow: 0 20px 45px rgba(15, 23, 42, 0.22); overflow: hidden; height: auto; min-height: 0;">
                <div class="form-header"
                    style="padding: 14px 20px; border-bottom: 1px solid #edf0f5; align-items: center;">
                    <h3 x-show="slideEditIndex === null" style="font-size: 16px;">@lang('form.name.create')
                        (Image Slider)</h3>
                    <h3 x-show="slideEditIndex !== null" style="font-size: 16px;">@lang('form.header.update', ['name' => 'Image Slider'])</h3>
                    <span style="cursor: pointer;" @click="closeSlideDialog()"><i data-feather="x"></i></span>
                </div>
                <div class="form-body overflow-y-auto" style="max-height: 64vh; padding: 18px 20px 8px;">
                    <div class="row-2">
                        <div class="form-row">
                            <label>@lang('form.body.label.ordering') <span>*</span></label>
                            <input type="number" x-model="slideForm.ordering"
                                placeholder="@lang('form.body.placeholder.ordering')" autocomplete="off">
                            <span class="error" x-show="slideValidate?.ordering" x-text="slideValidate?.ordering"></span>
                        </div>
                        <div class="form-row">
                            <label>@lang('form.body.label.image')</label>
                            <input type="file" accept="image/*" class="!p-[12px]" x-ref="slideImageInput"
                                @change="onPreviewSlideImage($event)">
                            <span class="error" x-show="slideValidate?.image" x-text="slideValidate?.image"></span>
                            <template x-if="slideForm.image_url">
                                <div
                                    class="h-[110px] rounded-md border border-gray-100 overflow-hidden relative grid place-items-center group mt-2">
                                    <img class="w-full h-full object-contain" :src="slideForm.image_url" alt="">
                                    <div class="absolute flex gap-2 opacity-0 group-hover:opacity-100 duration-[0.2s]">
                                        <button type="button"
                                            class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                            @click="onViewImage(slideForm.image_url)">
                                            <span class="material-icons-outlined text-white text-2xl w-[24px]">
                                                visibility_on
                                            </span>
                                        </button>
                                        <button type="button"
                                            class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                            @click="onRemoveSlideImage()">
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
                        <button type="button" @click="closeSlideDialog()">
                            <span>@lang('dialog.button.close')</span>
                        </button>
                        <button type="button" color="primary" @click="onSaveSlide()" :disabled="slideLoading">
                            <span class="material-icons mr-1">save</span>
                            <span>@lang('form.button.save')</span>
                            <div class="loader" style="display: none" x-show="slideLoading"></div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
    <script src="{{ asset('plugin/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    <script type="module">
        Alpine.data('ProductionPage', () => ({
            locale: @json(config('dummy.locale.en')),
            form: new FormGroup({
                page: ['production', ['required']],
                status: ['ACTIVE', ['required']],
            }),
            id: null,
            validate: null,
            loading: false,
            detailLoading: false,
            slideLoading: false,
            dataDetail: [],
            imageSlides: [],
            detailDialogOpen: false,
            detailEditIndex: null,
            detailValidate: null,
            detailForm: {},
            slideDialogOpen: false,
            slideEditIndex: null,
            slideValidate: null,
            slideForm: {},
            baseUrl: "{{ asset('storage/list-of-value') }}/",
            baseStorageUrl: "{{ asset('storage') }}/",
            init() {
                feather.replace();
                this.detailForm = this.emptyDetail();
                this.slideForm = this.emptySlide();

                let data = @json($page);
                if (data) {
                    this.id = data.id;
                    this.applySavedPage(data);
                }
            },
            async initDetailTinymce(descEn, descKm) {
                tinymce.remove('#prod-detail-en, #prod-detail-km');
                await tinymce.init({
                    relative_urls: false,
                    selector: 'textarea#prod-detail-en,textarea#prod-detail-km',
                    height: 300,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'wordcount'
                    ],
                    toolbar: 'fullscreen | bold italic underline | addImage media link | numlist bullist | styles | alignleft aligncenter alignright alignjustify | outdent indent',
                    setup: function(editor) {
                        editor.ui.registry.addButton('addImage', {
                            text: 'Image',
                            icon: 'image',
                            onAction: () => {
                                fileManager({
                                    multiple: true,
                                    afterClose: (result, basePath) => {
                                        if (result && result.length > 0) {
                                            result.map((file) => {
                                                const img = editor.dom.createHTML('img', {
                                                    src: basePath + file.path,
                                                    style: 'width:100% !important;'
                                                });
                                                editor.insertContent(img);
                                            });
                                        }
                                    }
                                });
                            }
                        });
                    },
                });
                tinymce.get('prod-detail-en')?.setContent(descEn ?? '');
                tinymce.get('prod-detail-km')?.setContent(descKm ?? '');
            },
            applySavedPage(data) {
                this.form.status = data?.status ?? 'ACTIVE';
                this.dataDetail = (data?.content?.dataDetail || []).map(item => this.normalizeDetail(item));
                this.imageSlides = (data?.content?.imageSlides || []).map(item => this.normalizeSlide(item));
            },
            emptyDetail() {
                return {
                    description_en: null,
                    description_km: null,
                    ordering: null,
                    image: null,
                    tmp_image: null,
                    image_url: null,
                };
            },
            emptySlide() {
                return {
                    ordering: null,
                    image: null,
                    tmp_image: null,
                    image_url: null,
                };
            },
            stripHtml(html) {
                if (!html) return '';
                const tmp = document.createElement('div');
                tmp.innerHTML = html;
                const text = tmp.textContent || tmp.innerText || '';
                return text.length > 100 ? text.substring(0, 100) + '...' : text;
            },
            resolveFileUrl(file) {
                if (!file) return null;
                if (file instanceof File) return URL.createObjectURL(file);
                if (file.startsWith('http') || file.startsWith('blob:')) return file;
                if (file.includes('/')) return this.baseStorageUrl + file;
                return this.baseUrl + file;
            },
            normalizeDetail(item = {}) {
                const image = item.image || item.tmp_image || null;
                return {
                    description_en: item.description_en ?? null,
                    description_km: item.description_km ?? null,
                    ordering: item.ordering ?? null,
                    image: item.image instanceof File ? item.image : null,
                    tmp_image: image instanceof File ? null : image,
                    image_url: item.image_url || this.resolveFileUrl(image),
                };
            },
            normalizeSlide(item = {}) {
                const image = item.image || item.tmp_image || null;
                return {
                    ordering: item.ordering ?? null,
                    image: item.image instanceof File ? item.image : null,
                    tmp_image: image instanceof File ? null : image,
                    image_url: item.image_url || this.resolveFileUrl(image),
                };
            },
            clone(data) {
                return {
                    ...data
                };
            },
            getNextOrdering(dataList) {
                return dataList.reduce((max, item) => Math.max(max, Number(item.ordering) || 0), 0) + 1;
            },
            openCreateDetailDialog() {
                this.detailEditIndex = null;
                this.detailValidate = null;
                this.detailForm = {
                    ...this.emptyDetail(),
                    ordering: this.getNextOrdering(this.dataDetail),
                };
                this.detailDialogOpen = true;
                this.$nextTick(async () => {
                    if (this.$refs.detailImageInput) this.$refs.detailImageInput.value = '';
                    feather.replace();
                    await this.initDetailTinymce('', '');
                });
            },
            openEditDetailDialog(index) {
                this.detailEditIndex = index;
                this.detailValidate = null;
                this.detailForm = this.clone(this.dataDetail[index]);
                this.detailDialogOpen = true;
                this.$nextTick(async () => {
                    if (this.$refs.detailImageInput) this.$refs.detailImageInput.value = '';
                    feather.replace();
                    await this.initDetailTinymce(
                        this.detailForm.description_en ?? '',
                        this.detailForm.description_km ?? ''
                    );
                });
            },
            validateDetailForm() {
                const required = '{{ __('validate.attributes.required') }}';
                const errors = {};
                if (!this.detailForm.description_en) errors.description_en = required;
                if (!this.detailForm.description_km) errors.description_km = required;
                if (this.detailForm.ordering === null || this.detailForm.ordering === '') errors.ordering = required;
                this.detailValidate = errors;
                return Object.keys(errors).length === 0;
            },
            async onSaveDetail() {
                this.detailForm.description_en = tinymce.get('prod-detail-en')?.getContent() ?? this.detailForm.description_en;
                this.detailForm.description_km = tinymce.get('prod-detail-km')?.getContent() ?? this.detailForm.description_km;
                if (this.detailLoading) return;
                if (!this.validateDetailForm()) {
                    const enErrors = Object.keys(this.detailValidate || {}).filter(k => k.includes('_en'));
                    const kmErrors = Object.keys(this.detailValidate || {}).filter(k => k.includes('_km'));
                    if (enErrors.length > 0) this.detailLocale = arrayLangLocale.en;
                    else if (kmErrors.length > 0) this.detailLocale = arrayLangLocale.km;
                    return;
                }

                const previousData = this.dataDetail.map(item => this.clone(item));
                const detail = this.normalizeDetail(this.detailForm);
                if (this.detailEditIndex === null) {
                    this.dataDetail.push(detail);
                } else {
                    this.dataDetail.splice(this.detailEditIndex, 1, detail);
                }

                const detailIndex = this.detailEditIndex === null ? this.dataDetail.length - 1 : this.detailEditIndex;
                const saved = await this.submitProduction('detail', detailIndex);
                if (saved) {
                    this.closeDetailDialog();
                    return;
                }

                this.dataDetail = previousData;
            },
            removeDetail(index) {
                this.$store.confirmDialog.open({
                    data: {
                        title: "@lang('dialog.title')",
                        message: "@lang('dialog.msg.delete')",
                        btnClose: "@lang('dialog.button.close')",
                        btnSave: "@lang('dialog.button.delete')",
                    },
                    afterClosed: async (result) => {
                        if (!result || this.loading) return;

                        const previousData = this.dataDetail.map(item => this.clone(item));
                        this.dataDetail.splice(index, 1);

                        const saved = await this.submitProduction();
                        if (!saved) {
                            this.dataDetail = previousData;
                        }
                    }
                });
            },
            closeDetailDialog() {
                tinymce.remove('#prod-detail-en, #prod-detail-km');
                this.detailDialogOpen = false;
                this.detailValidate = null;
            },
            onPreviewDetailImage(event) {
                const file = event.target.files[0];
                if (!file) return;
                this.detailForm.image = file;
                this.detailForm.tmp_image = null;
                this.detailForm.image_url = URL.createObjectURL(file);
            },
            onRemoveDetailImage() {
                this.detailForm.image = null;
                this.detailForm.tmp_image = null;
                this.detailForm.image_url = null;
                if (this.$refs.detailImageInput) this.$refs.detailImageInput.value = '';
            },
            openCreateSlideDialog() {
                this.slideEditIndex = null;
                this.slideValidate = null;
                this.slideForm = {
                    ...this.emptySlide(),
                    ordering: this.getNextOrdering(this.imageSlides),
                };
                this.slideDialogOpen = true;
                this.$nextTick(() => {
                    if (this.$refs.slideImageInput) this.$refs.slideImageInput.value = '';
                    feather.replace();
                });
            },
            openEditSlideDialog(index) {
                this.slideEditIndex = index;
                this.slideValidate = null;
                this.slideForm = this.clone(this.imageSlides[index]);
                this.slideDialogOpen = true;
                this.$nextTick(() => {
                    if (this.$refs.slideImageInput) this.$refs.slideImageInput.value = '';
                    feather.replace();
                });
            },
            validateSlideForm() {
                const required = '{{ __('validate.attributes.required') }}';
                const errors = {};
                if (this.slideForm.ordering === null || this.slideForm.ordering === '') errors.ordering = required;
                if (!this.slideForm.image && !this.slideForm.tmp_image) errors.image = required;
                this.slideValidate = errors;
                return Object.keys(errors).length === 0;
            },
            async onSaveSlide() {
                if (this.slideLoading || !this.validateSlideForm()) return;

                const previousData = this.imageSlides.map(item => this.clone(item));
                const slide = this.normalizeSlide(this.slideForm);
                if (this.slideEditIndex === null) {
                    this.imageSlides.push(slide);
                } else {
                    this.imageSlides.splice(this.slideEditIndex, 1, slide);
                }

                const slideIndex = this.slideEditIndex === null ? this.imageSlides.length - 1 : this.slideEditIndex;
                const saved = await this.submitProduction('slide', slideIndex);
                if (saved) {
                    this.closeSlideDialog();
                    return;
                }

                this.imageSlides = previousData;
            },
            removeSlide(index) {
                this.$store.confirmDialog.open({
                    data: {
                        title: "@lang('dialog.title')",
                        message: "@lang('dialog.msg.delete')",
                        btnClose: "@lang('dialog.button.close')",
                        btnSave: "@lang('dialog.button.delete')",
                    },
                    afterClosed: async (result) => {
                        if (!result || this.loading) return;

                        const previousData = this.imageSlides.map(item => this.clone(item));
                        this.imageSlides.splice(index, 1);

                        const saved = await this.submitProduction();
                        if (!saved) {
                            this.imageSlides = previousData;
                        }
                    }
                });
            },
            closeSlideDialog() {
                this.slideDialogOpen = false;
                this.slideValidate = null;
            },
            onPreviewSlideImage(event) {
                const file = event.target.files[0];
                if (!file) return;
                this.slideForm.image = file;
                this.slideForm.tmp_image = null;
                this.slideForm.image_url = URL.createObjectURL(file);
            },
            onRemoveSlideImage() {
                this.slideForm.image = null;
                this.slideForm.tmp_image = null;
                this.slideForm.image_url = null;
                if (this.$refs.slideImageInput) this.$refs.slideImageInput.value = '';
            },
            onViewImage(path) {
                Fancybox.show([{
                    src: path,
                    type: "image",
                }, ], {
                    on: {
                        ready: () => {
                            document.querySelector('.fancybox__container').style.zIndex = this.$store.libs.getLastIndex() +
                                1;
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
                    formData.append(`dataDetail[${index}][description_en]`, item.description_en ?? '');
                    formData.append(`dataDetail[${index}][description_km]`, item.description_km ?? '');
                    formData.append(`dataDetail[${index}][ordering]`, item.ordering ?? '');
                    if (item.image instanceof File) {
                        formData.append(`dataDetail[${index}][image]`, item.image);
                    }
                    if (item.tmp_image) {
                        formData.append(`dataDetail[${index}][tmp_image]`, item.tmp_image);
                    }
                });

                this.imageSlides.forEach((item, index) => {
                    formData.append(`imageSlides[${index}][ordering]`, item.ordering ?? '');
                    if (item.image instanceof File) {
                        formData.append(`imageSlides[${index}][image]`, item.image);
                    }
                    if (item.tmp_image) {
                        formData.append(`imageSlides[${index}][tmp_image]`, item.tmp_image);
                    }
                });

                return formData;
            },
            getDetailServerErrors(errors, index) {
                if (index === null || !errors) return {};

                return ['description_en', 'description_km', 'ordering', 'image'].reduce((carry, field) => {
                    const key = `dataDetail.${index}.${field}`;
                    if (errors[key]) {
                        carry[field] = Array.isArray(errors[key]) ? errors[key][0] : errors[key];
                    }
                    return carry;
                }, {});
            },
            getSlideServerErrors(errors, index) {
                if (index === null || !errors) return {};

                return ['ordering', 'image'].reduce((carry, field) => {
                    const key = `imageSlides.${index}.${field}`;
                    if (errors[key]) {
                        carry[field] = Array.isArray(errors[key]) ? errors[key][0] : errors[key];
                    }
                    return carry;
                }, {});
            },
            async submitProduction(source = null, index = null) {
                if (source === 'detail') {
                    this.detailLoading = true;
                } else if (source === 'slide') {
                    this.slideLoading = true;
                } else {
                    this.form.disable();
                    this.loading = true;
                }

                try {
                    const res = await Axios.post(`{{ route('admin-page-production-save') }}`, this.buildFormData(), {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });

                    this.id = res.data.id;
                    this.validate = null;
                    this.applySavedPage(res.data.data);
                    this.detailValidate = null;
                    this.slideValidate = null;

                    Toast({
                        message: res.data.message,
                        status: res.data.status,
                        size: 'small',
                    });

                    return true;
                } catch (e) {
                    const errors = e.response?.data?.errors;
                    this.validate = errors;

                    if (source === 'detail') {
                        const detailErrors = this.getDetailServerErrors(errors, index);
                        if (Object.keys(detailErrors).length > 0) {
                            this.detailValidate = detailErrors;
                            const enErrors = Object.keys(detailErrors).filter(k => k.includes('_en'));
                            const kmErrors = Object.keys(detailErrors).filter(k => k.includes('_km'));
                            if (enErrors.length > 0) this.detailLocale = arrayLangLocale.en;
                            else if (kmErrors.length > 0) this.detailLocale = arrayLangLocale.km;
                        }
                    }

                    if (source === 'slide') {
                        const slideErrors = this.getSlideServerErrors(errors, index);
                        if (Object.keys(slideErrors).length > 0) {
                            this.slideValidate = slideErrors;
                        }
                    }

                    return false;
                } finally {
                    if (source === 'detail') {
                        this.detailLoading = false;
                    } else if (source === 'slide') {
                        this.slideLoading = false;
                    } else {
                        this.form.enable();
                        this.loading = false;
                    }
                }
            }
        }));
    </script>
@endsection
