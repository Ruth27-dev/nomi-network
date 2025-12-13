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
                        <label>@lang('form.body.label.code')</label>
                        <input type="text" placeholder="@lang('form.body.placeholder.code')" x-model="form.code"
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
                        <label>@lang('form.body.label.description_en') </label>
                        <textarea type="text" placeholder="@lang('form.body.placeholder.description_en')" x-model="form.description_en" :disabled="form.disabled"
                            autocomplete="off">
                        </textarea>
                        <span class="error" x-show="validate?.description_en" x-text="validate?.description_en"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.description_km')</label>
                        <textarea type="text" placeholder="@lang('form.body.placeholder.description_km')" x-model="form.description_km" :disabled="form.disabled"
                            autocomplete="off">
                        </textarea>
                        <span class="error" x-show="validate?.description_km" x-text="validate?.description_km"></span>
                    </div>
                </div>
                <section class="border-b border-[#d8dce5] pb-3">
                    <div class="form-row !flex-row !justify-between m-0 border-b border-[#d8dce5]">
                        <label>@lang('form.body.label.product_variate')</label>

                    </div>
                    <template x-for="(variate, index) in product_variates">
                        <div>
                            <div class="row flex justify-end">
                                <span class="material-icons-outlined cursor-pointer text-green-500"
                                    @click="addMoreItemVariate(index)">add_circle</span>
                                <span class="material-icons-outlined cursor-pointer text-rose-400 ml-[7px]"
                                    @click="onRemoveItemVariate(index)">remove_circle</span>
                            </div>
                            <div class="row  border border-[#d8dce5] p-3 rounded mb-[10px] relative">
                                <div class="row-3">
                                    <div class="form-row">
                                        <label>@lang('form.body.label.title_en') </label>
                                        <input type="text" placeholder="@lang('form.body.placeholder.title_en')"
                                            x-model="variate.title_en" :disabled="form.disabled" autocomplete="off">
                                        <span class="error"
                                            x-show="validate?.['product_variates.' + index + '.title_en']"
                                            x-text="validate?.['product_variates.' + index + '.title_en']"></span>
                                    </div>
                                    <div class="form-row">
                                        <label>@lang('form.body.label.title_km')</label>
                                        <input type="text" placeholder="@lang('form.body.placeholder.title_km')"
                                            x-model="variate.title_km" :disabled="form.disabled" autocomplete="off">
                                        <span class="error"
                                            x-show="validate?.['product_variates.' + index + '.title_km']"
                                            x-text="validate?.['product_variates.' + index + '.title_km']"></span>
                                    </div>
                                    <div class="form-row">
                                        <label>@lang('form.body.label.status') </label>
                                        <select x-model="variate.status" :disabled="form.disabled">
                                            <option value="ACTIVE">Active</option>
                                            <option value="INACTIVE">Inactive</option>
                                        </select>
                                        <span class="error" x-show="validate?.status"
                                            x-text="validate?.status"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-row">
                                        <label>@lang('form.body.label.image')</label>
                                        <input type="file" :disabled="form.disabled" accept="image/*"
                                            :id="`image-${index}`" class="!p-[12px]"
                                            @change="onPreviewImage($el, index)">
                                        <input type="hidden" x-model="variate.tmp_file">
                                        <template x-if="variate.image_url">
                                            <div
                                                class="h-[250px] rounded-md border border-gray-100 overflow-hidden relative grid place-items-center group mt-2">
                                                <img class="w-full h-full object-contain" :src="variate.image_url"
                                                    alt="">
                                                <div
                                                    class="absolute flex gap-2 opacity-0 group-hover:opacity-100 duration-[0.2s]">
                                                    <button type="button"
                                                        class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                                        @click="onViewImage(variate.image_url)">
                                                        <span
                                                            class="material-icons-outlined text-white text-2xl w-[24px]">
                                                            visibility_on
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="row-2">
                                    <div class="form-row">
                                        <label>@lang('form.body.label.price') <span>*</span></label>
                                        <input type="number" placeholder="@lang('form.body.placeholder.price')"
                                            x-model="variate.price" :disabled="form.disabled" autocomplete="off">
                                        <span class="error"
                                            x-show="validate?.['product_variates.' + index + '.price']"
                                            x-text="validate?.['product_variates.' + index + '.price']"></span>
                                    </div>
                                    <div class="form-row">
                                        <label>@lang('form.body.label.size')</label>
                                        <input type="text" placeholder="@lang('form.body.placeholder.size')" x-model="variate.size"
                                            :disabled="form.disabled" autocomplete="off">
                                        <span class="error" x-show="validate?.size" x-text="validate?.size"></span>
                                    </div>
                                    <div class="form-row">
                                        <label>@lang('form.body.label.description_en')</label>
                                        <textarea type="text" placeholder="@lang('form.body.placeholder.description_en')" x-model="variate.description_en" :disabled="form.disabled"
                                            autocomplete="off">
                                    </textarea>
                                        <span class="error"
                                            x-show="validate?.['product_variates.' + index + '.description_en']"
                                            x-text="validate?.['product_variates.' + index + '.description_en']"></span>
                                    </div>
                                    <div class="form-row">
                                        <label>@lang('form.body.label.description_km')</label>
                                        <textarea type="text" placeholder="@lang('form.body.placeholder.description_km')" x-model="variate.description_km" :disabled="form.disabled"
                                            autocomplete="off">
                                    </textarea>
                                        <span class="error"
                                            x-show="validate?.['product_variates.' + index + '.description_km']"
                                            x-text="validate?.['product_variates.' + index + '.description_km']"></span>
                                    </div>
                                    <div class="form-row">
                                        <label>@lang('form.body.label.note_en')</label>
                                        <input type="text" placeholder="@lang('form.body.placeholder.note_en')"
                                            x-model="variate.note_en" :disabled="form.disabled" autocomplete="off">
                                        <span class="error" x-show="validate?.note_en"
                                            x-text="validate?.note_en"></span>
                                    </div>
                                    <div class="form-row">
                                        <label>@lang('form.body.label.note_km')</label>
                                        <input type="text" placeholder="@lang('form.body.placeholder.note_km')"
                                            x-model="variate.note_km" :disabled="form.disabled" autocomplete="off">
                                        <span class="error" x-show="validate?.note_km"
                                            x-text="validate?.note_km"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </template>
                </section>
                <div class="row">
                    <div class="form-row">
                        <label>@lang('form.body.label.image')</label>
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
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
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
                branch_id: ['', ['required']],
                branch_title: ['', ['required']],
                code: ['', []],
                title_en: ['', []],
                title_km: ['', []],
                status: ['ACTIVE', []],
                description_en: ['', []],
                description_km: ['', []],
                image: ['', []],
                tmp_file: ['', []],
                type: ["{{ $type }}", []],
                display_type: ["{{ config('dummy.item_type.customer.key') }}", []],
                category_ids: [
                    [],
                    []
                ],
                category_title: [
                    '',
                    []
                ],
            }),
            product_variates: [{
                product_variate_id: '',
                title_en: '',
                title_km: '',
                status: "ACTIVE",
                image: '',
                image_url: '',
                tmp_file: '',
                price: '',
                size: '',
                description_en: '',
                description_km: '',
                note_en: '',
                note_km: '',
                is_note: false,
                is_available: false
            }],
            selected_categories: [],
            image_url: null,
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
                } else {
                    this.form.is_sellable = this.form.type == 'customer' ? true : false;
                    this.form.is_consumable = this.form.type == 'inventory' ? true : false;
                }
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
                this.image_url = null;
                this.product_variates = [];
                this.addMoreItemVariate();
            },
            setValue(data) {
                this.form.code = data?.code;
                this.form.title_en = data?.title?.en;
                this.form.title_km = data?.title?.km;
                this.form.status = data?.status;
                this.form.description_en = data?.description?.en;
                this.form.description_km = data?.description?.km;
                this.form.is_sellable = data?.is_sellable;
                this.form.is_consumable = data?.is_consumable;
                this.form.unit_id = data?.unit?.id;
                this.form.unit_title = data?.unit?.title?.en;
                this.form.display_type = data?.type ?? "{{ config('dummy.item_type.customer.key') }}";

                if (data.categories && data.categories.length > 0) {
                    this.selected_categories = data.categories.map(item => {
                        return {
                            _id: item?.id,
                            _title: item?.title?.en,
                            _description: item?.description?.en,
                        }
                    });
                    this.form.category_ids = this.selected_categories.map(item => item._id);
                    this.form.category_title = this.selected_categories.map(item => item._title).join(', ');
                }

                if (data?.image) {
                    this.image_url = this?.data?.image ? this.data?.image : null;
                    this.form.tmp_file = data.image;
                }

                if (data?.product_variates) {
                    this.product_variates = [];
                    this.product_variates = data.product_variates.map(item => ({
                        product_variate_id: item.id ?? '',
                        title_en: item.title.en ?? '',
                        title_km: item.title.km ?? '',
                        status: item.status ?? '',
                        image: item?.image ?? '',
                        image_url: item?.image ? item?.image : '',
                        tmp_file: item?.image ?? '',
                        price: item?.product?.price ?? '',
                        size: item?.product?.size ?? '',
                        description_en: item?.product?.description?.en ?? '',
                        description_km: item?.product?.description?.km ?? '',
                        note_en: item?.product?.note?.en ?? '',
                        note_km: item?.product?.note?.km ?? '',
                        is_note: item?.product?.is_note ?? false,
                        is_available: item?.product?.is_available ?? false,
                    }));

                }
                if (data?.branch) {
                    this.form.branch_id = data?.branch?.id;
                    this.form.branch_title = data?.branch?.title?.en;
                }
            },
            onPreviewImage(el, index = null) {
                const profile = URL.createObjectURL(el.files[0]);
                if (index !== null) {
                    this.product_variates[index].image_url = profile;
                    return;
                }
                this.image_url = profile;
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
            addMoreItemVariate() {
                this.product_variates.push({
                    product_variate_id: '',
                    title_en: '',
                    title_km: '',
                    status: "ACTIVE",
                    image: '',
                    image_url: '',
                    tmp_file: '',
                    price: '',
                    size: '',
                    description_en: '',
                    description_km: '',
                    note_en: '',
                    note_km: '',
                    is_note: false,
                    is_available: false
                });
            },
            onRemoveItemVariate(index) {
                if (this.product_variates[index].product_variate_id) {
                    toastr.info("Can't remove Item Variate!", {
                        progressBar: true,
                        timeOut: 5000
                    });
                } else {
                    this.product_variates.splice(index, 1);
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
                                    branch_id: this.form.branch_id,
                                    type: this.form.type,
                                }
                            })
                            .then(response => {
                                const data = response?.data?.map(item => {
                                    return {
                                        _id: item.id,
                                        _title: item.title.en,
                                        _description: item.description.en,
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
                                        branch_id: this.form.branch_id,
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
                                                _description: item.description.en,
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
            async onValidate(callback) {
                this.validate = null;
                this.loading = true;
                this.form.disable();
                const data = this.form.value();
                await Axios({
                    url: `{{ route('admin-validation-product') }}`,
                    method: 'POST',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                    data: {
                        ...data,
                        id: this.dialogData?.id,
                        product_variates: this.product_variates,
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
            getImageBinary() {
                if (this.product_variates.length > 0) {
                    this.product_variates.forEach((item, index) => {
                        if (!item.image || item.tmp_file == '') {
                            const file = document.querySelector(`#image-${index}`);
                            item.image = file.files[0] ?? '';
                            console.log(item.image);
                        }
                    });
                }
            },
            async onSave() {
                await this.onValidate((res) => {
                    this.validate = res.response.data.errors;
                    if (res.status == 422) {
                        toastr.info(res.response.data.message, {
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
                                this.form.disable();
                                this.loading = true;
                                let file = document.querySelector('#image');
                                this.form.image = file.files[0] ?? '';
                                this.getImageBinary();
                                console.log(this.product_variates);

                                const data = this.form.value();

                                Axios({
                                    url: `{{ route('admin-product-save') }}`,
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'multipart/form-data',
                                    },
                                    data: {
                                        ...data,
                                        id: this.dialogData?.id,
                                        product_variates: this.product_variates,
                                    }
                                }).then((res) => {
                                    if (res.data.error == false) {
                                        this.form.reset();
                                        this.$dialog('storeItemDialog').close(true);
                                    }
                                    toastr.success(res.data.message, {
                                        progressBar: true,
                                        timeOut: 5000
                                    });
                                }).catch((e) => {
                                    this.validate = e.response.data.errors;
                                    toastr.info(e?.response?.data?.message, {
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
