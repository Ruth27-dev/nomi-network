<template x-dialog="storeItemDialog">
    <div x-data="storeItemDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 x-show="!data" class="text-gray-600"> @lang('form.header.create', ['name' => __('form.title.product')])</h3>
                <h3 x-show="dialogData?.id">
                    @lang('form.header.update', ['name' => __('form.title.product')])
                </h3>
                <span @click="$dialog('storeItemDialog').close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto pr-3" x-data="{ show_password: false, show_confirm_password: false }">
                <div class="row-2">
                    <div class="form-row">
                        <label>SKU</label>
                        <input type="text" placeholder="SKU" x-model="form.code"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.code" x-text="validate?.code"></span>
                    </div>
                    <div class="form-row">
                        <label for="status">@lang('form.body.label.status')<span>*</span> </label>
                        <select id="status" x-model="form.status" :disabled="form.disabled">
                            <option value="ACTIVE">Active</option>
                            <option value="INACTIVE">Inactive</option>
                        </select>
                        <span class="error" x-show="validate?.status" x-text="validate?.status"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.title_en')<span>*</span> </label>
                        <input type="text" placeholder="@lang('form.body.placeholder.title_en')" x-model="form.title_en"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.title_en" x-text="validate?.title_en"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.title_km')</label>
                        <input type="text" placeholder="@lang('form.body.placeholder.title_km')" x-model="form.title_km"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.title_km" x-text="validate?.title_km"></span>
                    </div>
                </div>
                <div class="form-row">
                    <label>@lang('form.body.label.category')<span>*</span> </label>
                    <input @click="selectCategory()" type="text" placeholder="@lang('form.body.placeholder.category')"
                        x-model="form.category_title" :disabled="form.disabled" autocomplete="off" readonly>
                    <span class="error" x-show="validate?.category_ids" x-text="validate?.category_ids"></span>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.description_en')</label>
                        <textarea id="product-desc-en" placeholder="@lang('form.body.placeholder.description_en')"></textarea>
                        <span class="error" x-show="validate?.description_en" x-text="validate?.description_en"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.description_km')</label>
                        <textarea id="product-desc-km" placeholder="@lang('form.body.placeholder.description_km')"></textarea>
                        <span class="error" x-show="validate?.description_km" x-text="validate?.description_km"></span>
                    </div>
                </div>
                <div class="row-3">
                    <div class="form-row">
                        <label>Base Price</label>
                        <input type="number" placeholder="0.00" x-model="form.price" min="0" step="0.01"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.price" x-text="validate?.price"></span>
                    </div>
                    <div class="form-row">
                        <label>Base Stock</label>
                        <input type="number" placeholder="0" x-model="form.stock" min="0"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.stock" x-text="validate?.stock"></span>
                    </div>
                    <div class="form-row">
                        <label>Pre-order</label>
                        <select x-model="form.is_preorder" :disabled="form.disabled">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label>Feature</label>
                        <select x-model="form.is_feature" :disabled="form.disabled">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Source (English)</label>
                        <textarea id="product-source-en" placeholder="Enter source (English)"></textarea>
                    </div>
                    <div class="form-row">
                        <label>Source (Khmer)</label>
                        <textarea id="product-source-kh" placeholder="Enter source (Khmer)"></textarea>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Location</label>
                        <input @click="selectLocation()" type="text" placeholder="Select location"
                            x-model="form.location_title" :disabled="form.disabled" autocomplete="off" readonly>
                    </div>
                </div>
                <section class="border-b border-[#d8dce5] pb-4">
                    <div class="flex items-center justify-between py-2 border-b border-[#d8dce5] mb-3">
                        <label class="font-medium text-gray-600 text-sm">Product Images</label>
                        <label :class="form.disabled ? 'opacity-50 pointer-events-none' : 'cursor-pointer'"
                            class="inline-flex items-center gap-1 text-sm px-3 py-1.5 rounded border border-primary text-primary hover:bg-primary/5 transition">
                            <span class="material-icons" style="font-size:16px">add_photo_alternate</span>
                            Add Images
                            <input type="file" multiple accept="image/*" class="hidden"
                                @change="onImageSelect($event)" :disabled="form.disabled">
                        </label>
                    </div>

                    {{-- image grid --}}
                    <div x-show="product_images.length > 0" class="grid grid-cols-4 gap-2 mb-1 justify-items-start">
                        <template x-for="(img, index) in product_images" :key="index">
                            <div class="rounded-lg border border-[#d8dce5] bg-gray-50 p-1.5 w-full max-w-[170px]">
                                <div class="relative group rounded overflow-hidden bg-white h-[120px]">
                                    {{-- uploading spinner --}}
                                    <div x-show="img.uploading"
                                        class="absolute inset-0 flex items-center justify-center bg-white/80 z-10">
                                        <div class="w-6 h-6 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
                                    </div>
                                    {{-- preview --}}
                                    <img :src="img.url" x-show="!img.uploading"
                                        class="w-full h-full object-cover">
                                </div>
                                <button type="button" @click="removeImage(index)" :disabled="form.disabled || img.uploading"
                                    class="mt-1.5 w-full inline-flex items-center justify-center gap-1 text-xs px-2 py-1 rounded border border-rose-300 text-rose-600 hover:bg-rose-50 disabled:opacity-50">
                                    <span class="material-icons" style="font-size:14px">delete</span>
                                    <span>Remove</span>
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- empty state --}}
                    <div x-show="product_images.length === 0"
                        class="flex flex-col items-center justify-center py-8 rounded-lg border-2 border-dashed border-[#d8dce5] text-gray-400 select-none">
                        <span class="material-icons text-3xl text-gray-300 mb-1">image</span>
                        <p class="text-sm">No images added yet</p>
                    </div>
                </section>

                <section class="border-b border-[#d8dce5] pb-4">
                    <div class="flex items-center justify-between py-2 border-b border-[#d8dce5] mb-3">
                        <label class="font-medium text-gray-600 text-sm">@lang('form.body.label.product_variate')</label>
                        <button type="button" @click="selectAttribute()" :disabled="form.disabled"
                            class="inline-flex items-center gap-1 text-sm px-3 py-1.5 rounded border border-primary text-primary hover:bg-primary/5 transition disabled:opacity-50">
                            <span class="material-icons" style="font-size:16px">tune</span>
                            Select Attribute
                        </button>
                    </div>

                    {{-- empty state --}}
                    <div x-show="product_variations.length === 0"
                        class="flex flex-col items-center justify-center py-10 text-gray-400 select-none">
                        <span class="material-icons text-4xl text-gray-300 mb-2">view_list</span>
                        <p class="text-sm">Select a product attribute to generate variations</p>
                    </div>

                    {{-- variation table --}}
                    <div x-show="product_variations.length > 0" class="overflow-x-auto rounded border border-[#d8dce5]">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-[#d8dce5]">
                                <tr>
                                    <th class="px-3 py-2 w-8">
                                        <input type="checkbox" @change="toggleAll($event)">
                                    </th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600 min-w-[130px]">Variants</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600 min-w-[110px]">SKU</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600 min-w-[100px]">Price</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600 min-w-[100px]">Discount</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600 min-w-[90px]">Stock Qty</th>
                                    <th class="px-3 py-2 text-center font-medium text-gray-600 w-20">In Stock</th>
                                    <th class="px-3 py-2 text-center font-medium text-gray-600 w-[72px]">Default</th>
                                    <th class="px-3 py-2 text-center font-medium text-gray-600 w-[65px]">Active</th>
                                    <th class="px-3 py-2 w-8"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(variate, index) in product_variations" :key="index">
                                    <tr class="border-b border-[#d8dce5] last:border-0 hover:bg-gray-50/60 transition">
                                        <td class="px-3 py-2 text-center">
                                            <input type="checkbox" x-model="variate.selected">
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded border border-[#d8dce5] flex items-center justify-center bg-gray-50 shrink-0">
                                                    <span class="material-icons text-gray-300" style="font-size:15px">image</span>
                                                </div>
                                                <span class="text-gray-700 font-medium" x-text="variate.title_en || '—'"></span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" x-model="variate.sku" :disabled="form.disabled"
                                                autocomplete="off" placeholder="SKU"
                                                class="w-full border border-[#d8dce5] rounded px-2 py-1 text-sm focus:outline-none focus:border-primary disabled:bg-gray-50">
                                            <span class="error text-xs" x-show="validate?.['product_variations.' + index + '.sku']"
                                                x-text="validate?.['product_variations.' + index + '.sku']"></span>
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="flex items-center border border-[#d8dce5] rounded overflow-hidden focus-within:border-primary">
                                                <span class="px-1.5 text-gray-400 bg-gray-50 border-r border-[#d8dce5] text-sm py-1 select-none">$</span>
                                                <input type="number" x-model="variate.price" :disabled="form.disabled"
                                                    autocomplete="off" placeholder="0" min="0"
                                                    class="w-full px-2 py-1 text-sm outline-none disabled:bg-gray-50">
                                            </div>
                                            <span class="error text-xs" x-show="validate?.['product_variations.' + index + '.price']"
                                                x-text="validate?.['product_variations.' + index + '.price']"></span>
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="flex items-center border border-[#d8dce5] rounded overflow-hidden focus-within:border-primary">
                                                <span class="px-1.5 text-gray-400 bg-gray-50 border-r border-[#d8dce5] text-sm py-1 select-none">$</span>
                                                <input type="number" x-model="variate.discount" :disabled="form.disabled"
                                                    autocomplete="off" placeholder="0" min="0"
                                                    class="w-full px-2 py-1 text-sm outline-none disabled:bg-gray-50">
                                            </div>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" x-model="variate.stock" :disabled="form.disabled"
                                                autocomplete="off" placeholder="0" min="0"
                                                class="w-full border border-[#d8dce5] rounded px-2 py-1 text-sm focus:outline-none focus:border-primary disabled:bg-gray-50">
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <input type="checkbox" x-model="variate.in_stock" :disabled="form.disabled">
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <input type="checkbox" x-model="variate.is_default" :disabled="form.disabled"
                                                @change="variate.is_default && onSetDefault(index)">
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <input type="checkbox" x-model="variate.is_active" :disabled="form.disabled">
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <button type="button" @click="onRemoveItemVariate(index)" :disabled="form.disabled"
                                                class="text-gray-400 hover:text-rose-500 transition disabled:opacity-40 cursor-pointer">
                                                <span class="material-icons" style="font-size:18px">more_vert</span>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
            <div class="form-footer">
                <div class="form-button">
                    <button type="button" color="primary" @click="onSave('close')"
                        :disabled="form.disabled || loading">
                        <span class="material-icons mr-1">save</span>
                        <span>Save & Close</span>
                        <div class="loader" style="display: none" x-show="loading"></div>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        Alpine.data('storeItemDialog', () => ({
            form: new FormGroup({
                code: ['', []],
                title_en: ['', []],
                title_km: ['', []],
                status: ['ACTIVE', []],
                description_en: ['', []],
                description_km: ['', []],
                price: ['', []],
                stock: ['', []],
                is_preorder: ['0', []],
                is_feature: ['0', []],
                type: ["{{ $type }}", []],
                category_ids: [[], []],
                category_title: ['', []],
                source_en: ['', []],
                source_kh: ['', []],
                product_location_id: ['', []],
                location_title: ['', []],
            }),
            product_variations: [],
            product_images: [],
            selected_attribute: null,
            selected_categories: [],
            dialogData: null,
            validate: null,
            loading: false,
            data: null,
            async init() {
                this.dialogData = this.$dialog('storeItemDialog').data;
                feather.replace();
                if (this.dialogData?.id) {
                    await this.fetchDataForUpdate(Number(this.dialogData?.id), (res) => {
                        this.data = res?.data;
                    });
                    this.setValue(this.data);
                }
                await this.initTinymce();
                this.$watch('form.disabled', (disabled) => {
                    ['product-desc-en', 'product-desc-km', 'product-source-en', 'product-source-kh'].forEach(id => {
                        const ed = tinymce.get(id);
                        if (ed) ed.mode.set(disabled ? 'readonly' : 'design');
                    });
                });
            },
            async initTinymce() {
                await tinymce.remove('#product-desc-en,#product-desc-km,#product-source-en,#product-source-kh');
                await tinymce.init({
                    relative_urls: false,
                    selector: '#product-desc-en,#product-desc-km,#product-source-en,#product-source-kh',
                    height: 300,
                    menubar: false,
                    toolbar_mode: 'sliding',
                    plugins: ['advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                        'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'wordcount'],
                    toolbar: 'fullscreen | bold italic underline | image media link | numlist bullist | table | code',
                    setup: (editor) => {
                        editor.on('init', () => {
                            const map = {
                                'product-desc-en':   this.form.description_en,
                                'product-desc-km':   this.form.description_km,
                                'product-source-en': this.form.source_en,
                                'product-source-kh': this.form.source_kh,
                            };
                            const content = map[editor.id];
                            if (content) editor.setContent(content);
                        });
                    },
                });
            },
            syncTinymce() {
                const map = {
                    'product-desc-en':   'description_en',
                    'product-desc-km':   'description_km',
                    'product-source-en': 'source_en',
                    'product-source-kh': 'source_kh',
                };
                Object.entries(map).forEach(([id, field]) => {
                    const ed = tinymce.get(id);
                    if (ed) this.form[field] = ed.getContent();
                });
            },
            async fetchDataForUpdate(id, callback) {
                await Axios({
                    url: `{{ route('admin-product-detail') }}`,
                    method: 'GET',
                    params: {
                        id: id,
                    }
                }).then((res) => {
                    callback(res.data)
                }).catch((e) => {
                    console.log(e);
                });
            },
            resetValue() {
                this.form.reset();
                this.product_variations = [];
                this.product_images = [];
                this.selected_attribute = null;
            },
            setValue(data) {
                this.form.code = data?.code;
                this.form.title_en = data?.title?.en;
                this.form.title_km = data?.title?.km;
                this.form.status = data?.status;
                this.form.description_en = data?.description?.en ?? '';
                this.form.description_km = data?.description?.km ?? '';
                this.form.price = data?.price ?? '';
                this.form.stock = data?.stock ?? '';
                this.form.is_preorder = data?.is_preorder ? '1' : '0';
                this.form.is_feature = data?.is_feature ? '1' : '0';

                if (data?.images?.length) {
                    this.product_images = data.images.map(g => ({
                        url: g.url,
                        path: g.image,
                        uploading: false,
                    }));
                }

                this.form.source_en = data?.source_en ?? '';
                this.form.source_kh = data?.source_kh ?? '';

                if (data?.product_location_id) {
                    this.form.product_location_id = data.product_location_id;
                    this.form.location_title = data.location?.name_en ?? '';
                }

                if (data?.category) {
                    this.selected_categories = [{
                        _id: data.category?.id,
                        _title: data.category?.title?.en,
                        _description: '',
                    }];
                    this.form.category_ids = [data.category?.id];
                    this.form.category_title = data.category?.title?.en ?? '';
                }

                if (data?.product_variations) {
                    this.product_variations = data.product_variations.map(item => ({
                        product_variation_id: item.id ?? '',
                        title_en: item.title?.en ?? '',
                        title_km: item.title?.km ?? '',
                        sku: item.sku ?? '',
                        price: item.price ?? '',
                        discount: item.discount ?? '',
                        stock: item.stock ?? '',
                        in_stock: item.in_stock !== undefined ? !!item.in_stock : true,
                        is_default: !!item.is_default,
                        is_active: (item.status ?? 'ACTIVE') === 'ACTIVE',
                        selected: false,
                    }));
                }
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
            makeVariation(titleEn = '') {
                return {
                    product_variation_id: '',
                    title_en: titleEn,
                    title_km: '',
                    sku: '',
                    price: '',
                    discount: '',
                    stock: '',
                    in_stock: true,
                    is_default: false,
                    is_active: true,
                    selected: false,
                };
            },
            selectAttribute() {
                SelectOption({
                    title: "Select Product Attribute",
                    placeholder: "Search...",
                    multiple: false,
                    selected: this.selected_attribute
                        ? { _id: this.selected_attribute.id, _title: this.selected_attribute.name }
                        : null,
                    unselect: false,
                    onReady: (callback_data) => {
                        Axios.get(`{{ route('admin-fetch-product-attribute-data') }}`).then((res) => {
                            const data = (res.data ?? []).map(item => ({
                                _id: item.id,
                                _title: item.name,
                                _description: item.code ?? '',
                            }));
                            callback_data(data);
                        });
                    },
                    onSearch: (value, callback_data) => {
                        Axios.get(`{{ route('admin-fetch-product-attribute-data') }}`, {
                            params: { search: value }
                        }).then((res) => {
                            const data = (res.data ?? []).map(item => ({
                                _id: item.id,
                                _title: item.name,
                                _description: item.code ?? '',
                            }));
                            callback_data(data);
                        });
                    },
                    afterClose: async (res) => {
                        if (!res) return;
                        const detail = await Axios.get(`{{ route('admin-product-attribute-detail') }}`, {
                            params: { id: res._id }
                        });
                        const attr = detail.data.data;
                        this.selected_attribute = { id: attr.id, name: attr.name };
                        const values = (attr.values ?? []).map(v => v.value).filter(Boolean);
                        this.product_variations = values.map(val => this.makeVariation(val));
                    },
                });
            },
            onSetDefault(activeIndex) {
                this.product_variations.forEach((v, i) => {
                    if (i !== activeIndex) v.is_default = false;
                });
            },
            toggleAll(event) {
                this.product_variations.forEach(v => v.selected = event.target.checked);
            },
            onRemoveItemVariate(index) {
                if (this.product_variations[index].product_variation_id) {
                    toastr.info("Can't remove a saved variation!", {
                        progressBar: true,
                        timeOut: 5000
                    });
                } else {
                    this.product_variations.splice(index, 1);
                }
            },
            selectCategory() {
                SelectOption({
                    title: "Select Category",
                    placeholder: "Search ...",
                    multiple: true,
                    selected: this.selected_categories,
                    unselect: true,
                    onReady: (callback_data) => {
                        Axios({
                                url: `{{ route('admin-fetch-category-data') }}`,
                                method: 'GET',
                                params: {
                                    type: this.form.type,
                                }
                            })
                            .then(response => {
                                const data = response?.data?.map(item => {
                                    return {
                                        _id: item.id,
                                        _title: item.title.en,
                                        _description: item.slug ?? '',
                                    }
                                });
                                callback_data(data);
                            });
                    },
                    onSearch: (value, callback_data) => {
                        queueSearch = setTimeout(() => {
                            Axios({
                                    url: `{{ route('admin-fetch-category-data') }}`,
                                    params: {
                                        search: value,
                                        type: this.form.type,
                                    },
                                    method: 'GET'
                                })
                                .then(response => {
                                    const data = response?.data?.map(
                                        item => {
                                            return {
                                                _id: item.id,
                                                _title: item.title.en,
                                                _description: item.slug ?? '',
                                            }
                                        });
                                    callback_data(data);
                                });
                        }, 1000);
                    },
                    afterClose: (res) => {
                        if (res) {
                            this.selected_categories = res;
                            this.form.category_ids = res.map(item => item._id);
                            this.form.category_title = res.map(item => item._title).join(', ');
                        } else {
                            this.selected_categories = [];
                            this.form.category_ids = [];
                            this.form.category_title = null;
                        }
                    }
                });
            },
            async onImageSelect(event) {
                const files = Array.from(event.target.files);
                event.target.value = '';
                for (const file of files) {
                    const idx = this.product_images.length;
                    this.product_images.push({ url: URL.createObjectURL(file), path: '', uploading: true });
                    const formData = new FormData();
                    formData.append('file', file);
                    await Axios.post(`{{ route('admin-product-image-upload') }}`, formData, {
                        headers: { 'Content-Type': 'multipart/form-data' },
                    }).then((res) => {
                        this.product_images[idx].path = res.data.path;
                        this.product_images[idx].url  = res.data.url;
                        this.product_images[idx].uploading = false;
                    }).catch(() => {
                        this.product_images.splice(idx, 1);
                        toastr.error('Failed to upload image', { progressBar: true, timeOut: 3000 });
                    });
                }
            },
            removeImage(index) {
                const image = this.product_images[index];
                if (!image || image.uploading) return;
                this.$store.confirmDialog.open({
                    data: {
                        title: "@lang('dialog.title')",
                        message: "Remove this image from product gallery?",
                        btnClose: "@lang('dialog.button.close')",
                        btnSave: "Remove",
                    },
                    afterClosed: (result) => {
                        if (result) {
                            this.product_images.splice(index, 1);
                        }
                    }
                });
            },
            selectLocation() {
                SelectOption({
                    title: "Select Location",
                    placeholder: "Search...",
                    multiple: false,
                    selected: this.form.product_location_id
                        ? { _id: this.form.product_location_id, _title: this.form.location_title }
                        : null,
                    unselect: true,
                    onReady: (callback_data) => {
                        Axios.get(`{{ route('admin-fetch-product-location-data') }}`).then((res) => {
                            callback_data((res.data ?? []).map(item => ({
                                _id: item.id,
                                _title: item.name_en,
                                _description: item.location_type ?? '',
                            })));
                        });
                    },
                    onSearch: (value, callback_data) => {
                        Axios.get(`{{ route('admin-fetch-product-location-data') }}`, { params: { search: value } }).then((res) => {
                            callback_data((res.data ?? []).map(item => ({
                                _id: item.id,
                                _title: item.name_en,
                                _description: item.location_type ?? '',
                            })));
                        });
                    },
                    afterClose: (res) => {
                        if (res) {
                            this.form.product_location_id = res._id;
                            this.form.location_title = res._title;
                        } else {
                            this.form.product_location_id = '';
                            this.form.location_title = '';
                        }
                    },
                });
            },
            async onValidate(callback) {
                this.syncTinymce();
                this.validate = null;
                this.loading = true;
                this.form.disable();
                const data = this.form.value();
                await Axios({
                    url: `{{ route('admin-validation-product') }}`,
                    method: 'POST',
                    data: {
                        ...data,
                        id: this.dialogData?.id,
                        images: this.product_images.filter(i => !i.uploading).map(i => i.path),
                        product_variations: this.product_variations.map(v => ({
                            ...v,
                            status: v.is_active ? 'ACTIVE' : 'INACTIVE',
                        })),
                    }
                }).then((response) => {
                    callback(response?.data);
                }).catch((error) => {
                    callback(error);
                }).finally(() => {
                    this.loading = false;
                    this.form.enable();
                });
            },
            async onSave() {
                await this.onValidate((res) => {
                    this.validate = res?.response?.data?.errors ?? null;
                    if (res?.status == 422) {
                        toastr.info(res?.response?.data?.message, {
                            progressBar: true,
                            timeOut: 5000
                        });
                    }
                });
                if (!this.validate) {
                    this.$store.confirmDialog.open({
                        data: {
                            title: "@lang('dialog.title')",
                            message: "@lang('dialog.msg.save')",
                            btnClose: "@lang('dialog.button.close')",
                            btnSave: "@lang('dialog.button.save')",
                        },
                        afterClosed: (result) => {
                            if (result) {
                                this.syncTinymce();
                                this.form.disable();
                                this.loading = true;
                                const data = this.form.value();

                                Axios({
                                    url: `{{ route('admin-product-save') }}`,
                                    method: 'POST',
                                    data: {
                                        ...data,
                                        id: this.dialogData?.id,
                                        images: this.product_images.filter(i => !i.uploading).map(i => i.path),
                                        product_variations: this.product_variations.map(v => ({
                                            ...v,
                                            status: v.is_active ? 'ACTIVE' : 'INACTIVE',
                                        })),
                                    }
                                }).then((res) => {
                                    if (res.data.error == false) {
                                        tinymce.remove('#product-desc-en,#product-desc-km,#product-source-en,#product-source-kh');
                                        this.form.reset();
                                        this.$dialog('storeItemDialog').close(true);
                                        toastr.success(res.data.message, {
                                            progressBar: true,
                                            timeOut: 5000
                                        });
                                    } else {
                                        toastr.error(res.data.message ?? 'Something went wrong!', {
                                            progressBar: true,
                                            timeOut: 5000
                                        });
                                    }
                                }).catch((e) => {
                                    this.validate = e?.response?.data?.errors ?? null;
                                    toastr.error(e?.response?.data?.message ?? 'Something went wrong!', {
                                        progressBar: true,
                                        timeOut: 5000
                                    });
                                }).finally(() => {
                                    this.form.enable();
                                    this.loading = false;
                                });
                            }
                        }
                    });
                }
            },
        }));
    </script>
</template>
