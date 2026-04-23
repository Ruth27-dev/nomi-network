<template x-dialog="storeVariationDialog">
    <div x-data="storeVariationDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 x-show="!dialogData?.id" class="text-gray-600">Create Product Variation</h3>
                <h3 x-show="dialogData?.id">Update Product Variation</h3>
                <span @click="$dialog('storeVariationDialog').close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto pr-3">
                <div class="row-2">
                    <div class="form-row">
                        <label>Product<span>*</span></label>
                        <input @click="selectProduct()" type="text" placeholder="Select Product"
                            x-model="form.product_title" :disabled="form.disabled" autocomplete="off" readonly>
                        <span class="error" x-show="validate?.product_id" x-text="validate?.product_id"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.status')<span>*</span></label>
                        <select x-model="form.status" :disabled="form.disabled">
                            <option value="ACTIVE">Active</option>
                            <option value="INACTIVE">Inactive</option>
                        </select>
                        <span class="error" x-show="validate?.status" x-text="validate?.status"></span>
                    </div>
                </div>
                <div class="row-2">
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
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.price') <span>*</span></label>
                        <input type="number" placeholder="@lang('form.body.placeholder.price')" x-model="form.price"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.price" x-text="validate?.price"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.size')</label>
                        <input type="text" placeholder="@lang('form.body.placeholder.size')" x-model="form.size"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.size" x-text="validate?.size"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.description_en')</label>
                        <textarea type="text" placeholder="@lang('form.body.placeholder.description_en')"
                            x-model="form.description_en" :disabled="form.disabled" autocomplete="off"></textarea>
                        <span class="error" x-show="validate?.description_en" x-text="validate?.description_en"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.description_km')</label>
                        <textarea type="text" placeholder="@lang('form.body.placeholder.description_km')"
                            x-model="form.description_km" :disabled="form.disabled" autocomplete="off"></textarea>
                        <span class="error" x-show="validate?.description_km" x-text="validate?.description_km"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.note_en')</label>
                        <input type="text" placeholder="@lang('form.body.placeholder.note_en')" x-model="form.note_en"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.note_en" x-text="validate?.note_en"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.note_km')</label>
                        <input type="text" placeholder="@lang('form.body.placeholder.note_km')" x-model="form.note_km"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.note_km" x-text="validate?.note_km"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-row">
                        <label>@lang('form.body.label.image')</label>
                        <input type="file" multiple accept="image/*" id="variation-images" class="!p-[12px]"
                            @change="onPreviewImages($event)">
                        <div class="grid grid-cols-4 gap-2 mt-2" x-show="image_urls.length > 0">
                            <template x-for="(img, imgIndex) in image_urls" :key="imgIndex">
                                <div
                                    class="h-[120px] rounded-md border border-gray-100 overflow-hidden relative grid place-items-center group">
                                    <img class="w-full h-full object-cover" :src="img" alt="">
                                    <div class="absolute flex gap-2 opacity-0 group-hover:opacity-100 duration-[0.2s]">
                                        <button type="button"
                                            class="bg-black/80 w-[35px] h-[35px] border border-white rounded-full grid place-items-center !pl-[4px]"
                                            @click="onViewImage(img)">
                                            <span class="material-icons-outlined text-white text-sm"
                                                style="margin-right: 10px;">
                                                visibility_on
                                            </span>
                                        </button>
                                        <button type="button"
                                            class="bg-red-500 w-[35px] h-[35px] rounded-full grid place-items-center"
                                            @click="removeImage(imgIndex)">
                                            <span class="material-icons-outlined text-white text-sm">
                                                close
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-footer">
                <div class="form-button">
                    <button type="button" color="primary" @click="onSave()" :disabled="form.disabled || loading">
                        <span class="material-icons mr-1">save</span>
                        <span>Save & Close</span>
                        <div class="loader" style="display: none" x-show="loading"></div>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        Alpine.data('storeVariationDialog', () => ({
            form: new FormGroup({
                product_id: ['', []],
                product_title: ['', []],
                title_en: ['', []],
                title_km: ['', []],
                status: ['ACTIVE', []],
                price: ['', []],
                size: ['', []],
                description_en: ['', []],
                description_km: ['', []],
                note_en: ['', []],
                note_km: ['', []],
                is_available: ['0', []],
            }),
            images: [],
            image_urls: [],
            tmp_files: [],
            dialogData: null,
            validate: null,
            loading: false,
            async init() {
                this.dialogData = this.$dialog('storeVariationDialog').data;
                feather.replace();
                if (this.dialogData?.id) {
                    await this.fetchDataForUpdate(Number(this.dialogData?.id), (res) => {
                        this.setValue(res?.data);
                    });
                }
            },
            async fetchDataForUpdate(id, callback) {
                await Axios({
                    url: `{{ route('admin-product-variation-detail') }}`,
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
            setValue(data) {
                this.form.product_id = data?.product_id ?? '';
                this.form.product_title = data?.product?.title?.[langLocale] ?? data?.product?.title?.en ?? '';
                this.form.title_en = data?.title?.en ?? '';
                this.form.title_km = data?.title?.km ?? '';
                this.form.status = data?.status ?? 'ACTIVE';
                this.form.price = data?.price ?? '';
                this.form.size = data?.size ?? '';
                this.form.description_en = data?.description?.en ?? '';
                this.form.description_km = data?.description?.km ?? '';
                this.form.note_en = data?.note?.en ?? '';
                this.form.note_km = data?.note?.km ?? '';
                this.form.is_available = data?.is_available ? '1' : '0';
                this.images = [];
                this.image_urls = data?.images ? data.images.map(img => img.url) : [];
                this.tmp_files = data?.images ? data.images.map(img => img.image) : [];
            },
            selectProduct() {
                SelectOption({
                    title: "Select Product",
                    placeholder: "Search ...",
                    multiple: false,
                    selected: this.form.product_id,
                    unselect: true,
                    onReady: (callback_data) => {
                        Axios({
                                url: `{{ route('admin-fetch-product-data') }}`,
                                method: 'GET',
                            })
                            .then(response => {
                                const data = response?.data?.map(item => {
                                    return {
                                        _id: item.id,
                                        _title: item.title?.[langLocale] ?? item.title?.en ?? 'Untitled',
                                        _description: item.description?.[langLocale] ?? item.description?.en ?? '',
                                    }
                                });
                                callback_data(data);
                            });
                    },
                    onSearch: (value, callback_data) => {
                        queueSearch = setTimeout(() => {
                            Axios({
                                    url: `{{ route('admin-fetch-product-data') }}`,
                                    params: {
                                        search: value,
                                    },
                                    method: 'GET'
                                })
                                .then(response => {
                                    const data = response?.data?.map(
                                        item => {
                                            return {
                                                _id: item.id,
                                                _title: item.title?.[langLocale] ?? item.title?.en ?? 'Untitled',
                                                _description: item.description?.[langLocale] ?? item.description?.en ??
                                                    '',
                                            }
                                        });
                                    callback_data(data);
                                });
                        }, 1000);
                    },
                    afterClose: (res) => {
                        if (res) {
                            this.form.product_id = res._id;
                            this.form.product_title = res._title;
                        } else {
                            this.form.product_id = '';
                            this.form.product_title = '';
                        }
                    }
                });
            },
            onPreviewImages(event) {
                const files = Array.from(event.target.files);
                files.forEach(file => {
                    this.images.push(file);
                    this.image_urls.push(URL.createObjectURL(file));
                });
                event.target.value = '';
            },
            removeImage(imageIndex) {
                this.image_urls.splice(imageIndex, 1);
                const existingCount = this.tmp_files.length;
                if (imageIndex < existingCount) {
                    this.tmp_files.splice(imageIndex, 1);
                } else {
                    const newIndex = imageIndex - existingCount;
                    if (this.images.length > newIndex) {
                        this.images.splice(newIndex, 1);
                    }
                }
            },
            onViewImage(path) {
                Fancybox.show([{
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
            async onValidate(callback) {
                this.validate = null;
                this.loading = true;
                this.form.disable();
                const data = this.form.value();
                await Axios({
                    url: `{{ route('admin-validation-product-variation') }}`,
                    method: 'POST',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                    data: {
                        ...data,
                        id: this.dialogData?.id,
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
                    this.validate = res.response?.data?.errors;
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
                                const data = this.form.value();
                                Axios({
                                    url: `{{ route('admin-product-variation-save') }}`,
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'multipart/form-data',
                                    },
                                    data: {
                                        ...data,
                                        id: this.dialogData?.id,
                                        images: this.images,
                                        tmp_files: this.tmp_files,
                                    }
                                }).then((res) => {
                                    if (res.data.error == false) {
                                        this.$dialog('storeVariationDialog').close(true);
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
