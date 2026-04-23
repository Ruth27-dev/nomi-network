<template x-dialog="storeProductDiscountDialog">
    <div x-data="storeProductDiscountDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 x-show="!dialogData?.id" class="text-gray-600">@lang('form.name.create') (@lang('form.name.product_discount'))</h3>
                <h3 x-show="dialogData?.id" class="text-gray-600">@lang('form.header.update') (@lang('form.name.product_discount'))</h3>
                <span @click="close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto pr-3">
                <div class="row">
                    <div class="form-row">
                        <label for="type">@lang('form.body.label.type')<span>*</span> </label>
                        <select id="type" x-model="form.type" :disabled="form.disabled">
                            <option :value="appConfig.discount.type.discount">@lang('form.name.discount')</option>
                            <option :value="appConfig.discount.type.coupon">@lang('form.name.coupon')</option>
                        </select>
                        <span class="error" x-show="validate?.type" x-text="validate?.type"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row" x-show="form.type == appConfig.discount.type.coupon">
                        <label>@lang('form.body.label.code')<span>*</span></label>
                        <input type="text" placeholder="OPW&822H" x-model="form.code" :disabled="form.disabled"
                            autocomplete="off">
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
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.title_en')<span>*</span></label>
                        <input type="text" placeholder="15% off on..." x-model="form.title_en"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.title_en" x-text="validate?.title_en"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.title_km')<span>*</span></label>
                        <input type="text" placeholder="15% off on..." x-model="form.title_km"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.title_km" x-text="validate?.title_km"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.discount_amount') <span>*</span></label>
                        <input type="text" placeholder="2" x-model="form.discount_amount" :disabled="form.disabled"
                            autocomplete="off">
                        <span class="error" x-show="validate?.discount_amount"
                            x-text="validate?.discount_amount"></span>
                    </div>
                    <div class="form-row">
                        <label for="discount_type">@lang('form.body.label.discount_type')<span>*</span> </label>
                        <select id="discount_type" x-model="form.discount_type" :disabled="form.disabled">
                            <option :value="appConfig.discount.type.percentage">@lang('form.name.percentage')</option>
                            <option :value="appConfig.discount.type.amount">@lang('form.name.amount')</option>
                        </select>
                        <span class="error" x-show="validate?.discount_type" x-text="validate?.discount_type"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.remark_en')</label>
                        <textarea type="text" placeholder="..." x-model="form.remark_en" :disabled="form.disabled" autocomplete="off">
                        </textarea>
                        <span class="error" x-show="validate?.remark_en" x-text="validate?.remark_en"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.remark_km')</label>
                        <textarea type="text" placeholder="..." x-model="form.remark_km" :disabled="form.disabled" autocomplete="off">
                        </textarea>
                        <span class="error" x-show="validate?.remark_km" x-text="validate?.remark_km"></span>
                    </div>
                </div>
                <section class="border-b border-[#d8dce5] pb-3">
                    <div class="form-row !items-center m-0 border-b border-[#d8dce5]">
                        <label>@lang('form.name.condition_and_rule')</label>
                    </div>
                    <div class="row-2">
                        <div class="form-row">
                            <label for="start_date">@lang('form.body.label.start_date')</label>
                            <input id="start_date" x-ref="start_date" type="text" placeholder="@lang('form.body.placeholder.date')"
                                x-model="form.start_date" :disabled="form.disabled" autocomplete="off" readonly>
                            <span class="error" x-show="validate?.start_date" x-text="validate?.start_date"></span>
                        </div>
                        <div class="form-row">
                            <label for="end_date">@lang('form.body.label.end_date')</label>
                            <input id="end_date" x-ref="end_date" type="text" placeholder="@lang('form.body.placeholder.date')"
                                x-model="form.end_date" :disabled="form.disabled" autocomplete="off" readonly>
                            <span class="error" x-show="validate?.end_date" x-text="validate?.end_date"></span>
                        </div>
                    </div>
                    <div class="row-2">
                        <div class="form-row">
                            <label>@lang('form.body.label.usage_limited')</label>
                            <input type="text" placeholder="5" x-model="form.usage_limit"
                                :disabled="form.disabled" autocomplete="off">
                            <span class="error" x-show="validate?.usage_limit"
                                x-text="validate?.usage_limit"></span>
                        </div>
                        <div class="form-row">
                            <label>@lang('form.body.label.usage_per_customer')</label>
                            <input type="text" placeholder="1" x-model="form.usage_per_customer"
                                :disabled="form.disabled" autocomplete="off">
                            <span class="error" x-show="validate?.usage_per_customer"
                                x-text="validate?.usage_per_customer"></span>
                        </div>
                        <div class="form-row">
                            <label>@lang('form.body.label.min_amount')</label>
                            <input type="text" placeholder="15" x-model="form.min_amount"
                                :disabled="form.disabled" autocomplete="off">
                            <span class="error" x-show="validate?.min_amount" x-text="validate?.min_amount"></span>
                        </div>
                        <div class="form-row">
                            <label>@lang('form.body.label.max_amount')</label>
                            <input type="text" placeholder="50" x-model="form.max_amount"
                                :disabled="form.disabled" autocomplete="off">
                            <span class="error" x-show="validate?.max_amount" x-text="validate?.max_amount"></span>
                        </div>
                    </div>
                    <div class="flex gap-[15px]">
                        <div class="form-row w-[70%]">
                            <label>@lang('form.body.label.products')</label>
                            <input @click="selectProductVariation()" type="text" placeholder="@lang('form.body.placeholder.select_products')"
                                x-model="form.product_title" :disabled="form.disabled" autocomplete="off" readonly>
                            <span class="error" x-show="validate?.product_ids"
                                x-text="validate?.product_ids"></span>
                        </div>
                        <div class="form-row w-[30%]">
                            <label for="product_discount_type">@lang('form.body.label.product_discount_type')<span>*</span> </label>
                            <select id="product_discount_type" x-model="form.is_flat_discount"
                                :disabled="form.disabled || form.product_ids.length == 0">
                                <option value="0">@lang('form.name.unit_discount')</option>
                                <option value="1">@lang('form.name.flat_discount')</option>
                            </select>
                            <span class="error" x-show="validate?.is_flat_discount"
                                x-text="validate?.is_flat_discount"></span>
                        </div>
                    </div>
                    <div class="row" x-show="form.product_ids">
                        <table class="border-collapse border border-[#d8dce5] w-full">
                            <thead>
                                <tr class="h-[35px]">
                                    <th class="border border-[#d8dce5] text-sm text-[#5a5e66] font-[600]">
                                        @lang('table.field.product')</th>
                                    <th class="border border-[#d8dce5] text-sm text-[#5a5e66] font-[600]">
                                        @lang('table.field.title')</th>
                                    <th class="border border-[#d8dce5] text-sm text-[#5a5e66] font-[600]">
                                        @lang('table.field.price')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(product, index) in selected_products" :key="index">
                                    <tr class="hover:bg-gray-100 h-[35px]">
                                        <td class="border border-[#d8dce5] text-sm text-[#5a5e66] px-2">
                                            <span x-text="product._product_title"></span>
                                        </td>
                                        <td class="border border-[#d8dce5] text-sm text-[#5a5e66] px-2">
                                            <span x-text="product._variation_title"></span>
                                        </td>
                                        <td class="border border-[#d8dce5] text-sm text-[#5a5e66] px-2 text-right">
                                            <span x-text="parseFloat(product._price).toFixed(2)"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </section>
                <div class="row mt-3">
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
        Alpine.data('storeProductDiscountDialog', () => ({
            form: new FormGroup({
                type: [appConfig.discount.type.coupon, []],
                code: ['', []],
                status: ['ACTIVE', []],
                title_en: ['', []],
                title_km: ['', []],
                discount_amount: ['', []],
                discount_type: [appConfig.discount.type.percentage, []],
                remark_en: ['', []],
                remark_km: ['', []],
                is_flat_discount: ['0', []],

                start_date: ['', []],
                end_date: ['', []],
                usage_limit: ['', []],
                usage_per_customer: ['', []],
                min_amount: ['', []],
                max_amount: ['', []],

                product_ids: [
                    [],
                    []
                ],
                product_title: [
                    '',
                    []
                ],
                tmp_file: ['', []],
                image: ['', []],
            }),
            selected_products: [],
            baseUrl: "{{ asset('storage/discount') }}/",
            image_url: null,
            dialogData: null,
            validate: null,
            loading: false,
            data: null,
            async init() {
                this.dialogData = this.$dialog('storeProductDiscountDialog').data;
                feather.replace();
                if (this.dialogData?.id) {
                    await this.fetchDataForUpdate(Number(this.dialogData?.id), (res) => {
                        this.data = res?.data;
                    });
                    this.setValue(this.data);
                }

                this.initDatePicker();
            },
            async fetchDataForUpdate(id, callback) {
                await Axios({
                    url: `{{ route('admin-product-discount-detail') }}`,
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
                this.form.type = data?.type;
                this.form.code = data?.code;
                this.form.status = data?.status;
                this.form.title_en = data?.title?.en;
                this.form.title_km = data?.title?.km;
                this.form.discount_amount = data?.discount_amount;
                this.form.discount_type = data?.discount_type;
                this.form.remark_en = data?.remark?.en;
                this.form.remark_km = data?.remark?.km;

                this.form.start_date = data?.start_date ? dateFormat(data?.start_date) : '';
                this.form.end_date = data?.end_date ? dateFormat(data?.end_date) : '';
                this.form.usage_limit = data?.usage_limit;
                this.form.usage_per_customer = data?.usage_per_customer;
                this.form.is_flat_discount = data?.is_flat_discount ?? 0;

                if (data?.conditions?.length > 0) {
                    let min = data?.conditions.find(item => item.type == 'MIN');
                    let max = data?.conditions.find(item => item.type == 'MAX');
                    this.form.min_amount = min?.amount ?? '';
                    this.form.max_amount = max?.amount ?? '';
                }

                if (data?.products?.length > 0) {
                    this.selected_products = data?.products.map((item) => {
                        return {
                            _id: item.id,
                            _product_title: item?.product?.title?.[langLocale] ?? item?.product?.title
                                ?.en ?? 'Untitled',
                            _variation_title: item?.title?.[langLocale] ?? item?.title?.en ?? '',
                            _price: item?.price ?? 0,
                            _size: item?.size ?? '',
                            _item: item,
                        }
                    });
                    this.form.product_ids = this.selected_products.map(item => item._id);
                    this.form.product_title = this.selected_products.map(item => item._variation_title).join(
                        ', ');
                }

                this.form.tmp_file = data?.image;
                this.image_url = data?.image ? this.baseUrl + data?.image : null;
            },
            onPreviewImage(el) {
                const profile = URL.createObjectURL(el.files[0]);
                this.image_url = profile;
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
            initDatePicker() {
                const dateRangeFormat = dateRangePickerInputFormat();
                $('#start_date, #end_date').daterangepicker({
                    showDropdowns: true,
                    singleDatePicker: true,
                    autoUpdateInput: false,
                    minYear: parseInt(moment().format('YYYY'), 10) - 1,
                    maxYear: parseInt(moment().format('YYYY'), 10) + 10,
                    autoApply: true,
                    opens: "center",
                    locale: {
                        format: dateRangeFormat,
                        cancelLabel: 'Clear',
                    }
                });
                $('#start_date').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format(dateRangeFormat));
                    $('#end_date').data('daterangepicker').minDate = picker.startDate;
                });
                $('#end_date').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format(dateRangeFormat));
                    $('#start_date').data('daterangepicker').maxDate = picker.startDate;
                });
                $('#start_date').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                });
                $('#end_date').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                });

                const initialFromDate = moment(this.form.start_date, dateRangeFormat);
                if (initialFromDate.isValid()) {
                    $('#start_date').data('daterangepicker').setStartDate(initialFromDate);
                    $('#start_date').data('daterangepicker').setEndDate(initialFromDate);
                    $('#start_date').val(initialFromDate.format(dateRangeFormat));
                }
                const initialToDate = moment(this.form.end_date, dateRangeFormat);
                if (initialToDate.isValid()) {
                    $('#end_date').data('daterangepicker').setStartDate(initialToDate);
                    $('#end_date').data('daterangepicker').setEndDate(initialToDate);
                    $('#end_date').val(initialToDate.format(dateRangeFormat));
                }
            },
            selectProductVariation() {
                SelectOption({
                    title: `@lang('form.body.placeholder.select_products')`,
                    placeholder: "@lang('form.search_filter.search')",
                    multiple: true,
                    selected: this.selected_products,
                    unselect: true,
                    onReady: (callback_data) => {
                        Axios({
                                url: `{{ route('admin-fetch-product-variation-data') }}`,
                                method: 'GET'
                            })
                            .then(response => {
                                const data = response?.data?.map(item => {
                                    return {
                                        _id: item.id,
                                        _title: item?.product?.title?.[langLocale] ??
                                            item?.product?.title?.en ?? 'Untitled',
                                        _description: item?.description?.[langLocale] ??
                                            item?.description?.en ?? '',
                                        _price: item?.price + " $" ?? 0 + " $",
                                        _size: item?.size ?? '',
                                        _item: item,
                                    }
                                });
                                callback_data(data);
                            });
                    },
                    onSearch: (value, callback_data) => {
                        queueSearch = setTimeout(() => {
                            Axios({
                                    url: `{{ route('admin-fetch-product-variation-data') }}`,
                                    params: {
                                        search: value
                                    },
                                    method: 'GET'
                                })
                                .then(response => {
                                    const data = response?.data?.map(
                                        item => {
                                            return {
                                                _id: item.id,
                                                _title: item?.product?.title?.[
                                                        langLocale
                                                    ] ?? item?.product?.title?.en ??
                                                    'Untitled',
                                                _description: item?.description?.[
                                                        langLocale
                                                    ] ?? item?.description?.en ??
                                                    '',
                                                _price: item?.price + " $" ?? 0 +
                                                    " $",
                                                _size: item?.size ?? '',
                                                _item: item,
                                            }
                                        });
                                    callback_data(data);
                                });
                        }, 1000);
                    },
                    afterClose: (res) => {
                        if (res) {
                            this.selected_products = res;
                            this.form.product_ids = res.map(item => item._id);
                            this.form.product_title = res.map(item => item._variation_title).join(
                                ', ');
                        } else {
                            this.selected_products = [];
                            this.form.product_ids = [];
                            this.form.product_title = null;
                        }
                    }
                });
            },
            close() {
                const start_date = $('#start_date').data('daterangepicker');
                const end_date = $('#end_date').data('daterangepicker');
                if (start_date) {
                    start_date.remove();
                }
                if (end_date) {
                    end_date.remove();
                }
                $('#start_date').val('');
                $('#end_date').val('');
                this.$dialog('storeProductDiscountDialog').close();
            },
            async onValidate(callback) {
                this.validate = null;
                this.loading = true;
                this.form.disable();
                this.form.start_date = $('#start_date').val();
                this.form.end_date = $('#end_date').val();
                const data = this.form.value();
                await Axios({
                    url: `{{ route('admin-validation-product-discount') }}`,
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
                                let file = document.querySelector('#image');
                                this.form.image = file.files[0] ?? '';
                                this.form.start_date = $('#start_date').val();
                                this.form.end_date = $('#end_date').val();
                                const data = this.form.value();

                                Axios({
                                    url: `{{ route('admin-product-discount-save') }}`,
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'multipart/form-data',
                                    },
                                    data: {
                                        ...data,
                                        id: this.dialogData?.id,
                                    }
                                }).then((res) => {
                                    if (res.data.error == false) {
                                        this.form.reset();
                                        this.$dialog('storeProductDiscountDialog')
                                            .close(true);
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
