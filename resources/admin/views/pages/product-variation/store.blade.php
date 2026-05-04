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
                        <label>SKU<span>*</span></label>
                        <input type="text" placeholder="SKU" x-model="form.sku"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.sku" x-text="validate?.sku"></span>
                    </div>
                    <div class="form-row">
                        <label>Barcode</label>
                        <input type="text" placeholder="Barcode" x-model="form.barcode"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.barcode" x-text="validate?.barcode"></span>
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
                        <label>Stock</label>
                        <input type="number" placeholder="Stock" x-model="form.stock"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.stock" x-text="validate?.stock"></span>
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
                sku: ['', []],
                barcode: ['', []],
                title_en: ['', []],
                title_km: ['', []],
                status: ['ACTIVE', []],
                price: ['', []],
                stock: ['', []],
            }),
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
                this.form.sku = data?.sku ?? '';
                this.form.barcode = data?.barcode ?? '';
                this.form.title_en = data?.title?.en ?? '';
                this.form.title_km = data?.title?.km ?? '';
                this.form.status = data?.status ?? 'ACTIVE';
                this.form.price = data?.price ?? '';
                this.form.stock = data?.stock ?? '';
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
            async onValidate(callback) {
                this.validate = null;
                this.loading = true;
                this.form.disable();
                const data = this.form.value();
                await Axios({
                    url: `{{ route('admin-validation-product-variation') }}`,
                    method: 'POST',
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
                                    data: {
                                        ...data,
                                        id: this.dialogData?.id,
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
