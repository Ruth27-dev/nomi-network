<template x-dialog="storeCopyItemDialog">
    <div x-data="storeCopyItemDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 x-show="!data" class="text-gray-600">Add New Item</h3>
                <h3 x-show="data" class="text-gray-600" x-text="'Update Item ( ' + data?.code + ' )'"></h3>
                <span @click="$dialog('storeCopyItemDialog').close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto pr-3" x-data="{ show_password: false, show_confirm_password: false }">
                <div class="row">
                    <div class="form-row">
                        <label>@lang('form.body.label.branch')</label>
                        <input @click="selectBranch()" type="text" placeholder="..." x-model="form.branch_title"
                            :disabled="form.disabled" autocomplete="off" readonly>
                        <span class="error" x-show="validate?.branch_title" x-text="validate?.branch_title"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Code<span>*</span> </label>
                        <input type="text" placeholder="IM001" x-model="form.code" :disabled="form.disabled"
                            autocomplete="off">
                        <span class="error" x-show="validate?.code" x-text="validate?.code"></span>
                    </div>
                    <div class="form-row">
                        <label for="status">Status<span>*</span> </label>
                        <select id="status" x-model="form.status" :disabled="form.disabled">
                            <option value="ACTIVE">Active</option>
                            <option value="INACTIVE">Inactive</option>
                        </select>
                        <span class="error" x-show="validate?.status" x-text="validate?.status"></span>
                    </div>
                    <div class="form-row">
                        <label>Title (EN) </label>
                        <input type="text" placeholder="ឈុតជ្រូកកណ្ដុរខ្វៃអំបិលម្ទេស" x-model="form.title_en"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.title_en" x-text="validate?.title_en"></span>
                    </div>
                    <div class="form-row">
                        <label>Title (KM) </label>
                        <input type="text" placeholder="ឈុតជ្រូកកណ្ដុរខ្វៃអំបិលម្ទេស" x-model="form.title_km"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.title_km" x-text="validate?.title_km"></span>
                    </div>
                </div>
                <div class="form-row">
                    <label>Category </label>
                    <input @click="selectCategory()" type="text" placeholder="Main Dish" x-model="form.category_title"
                        :disabled="form.disabled || !form.branch_id" autocomplete="off" readonly>
                    <span class="error" x-show="validate?.category_ids" x-text="validate?.category_ids"></span>
                </div>
                <div class="form-row">
                    <label>@lang('form.body.label.type') <span>*</span></label>
                    <select x-model="form.display_type" :disabled="form.disabled">
                        <option value="{{config('dummy.item_type.customer.key')}}">@lang('table.field.customer')</option>
                        <option value="{{config('dummy.item_type.pos.key')}}">@lang('table.field.pos')</option>
                    </select>
                    <span class="error" x-show="validate?.display_type" x-text="validate?.display_type"></span>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Description (EN) </label>
                        <textarea type="text" placeholder="..." x-model="form.description_en"
                            :disabled="form.disabled" autocomplete="off">
                        </textarea>
                        <span class="error" x-show="validate?.description_en" x-text="validate?.description_en"></span>
                    </div>
                    <div class="form-row">
                        <label>Description (KM) </label>
                        <textarea type="text" placeholder="..." x-model="form.description_km"
                            :disabled="form.disabled" autocomplete="off">
                        </textarea>
                        <span class="error" x-show="validate?.description_km" x-text="validate?.description_km"></span>
                    </div>
                </div>
                <section class="border-b border-[#d8dce5] pb-3">
                    <div class="form-row !items-center m-0 border-b border-[#d8dce5]">
                        <label>Sale & Inventory Info</label>
                    </div>
                    <div class="form-row">
                        <label>Unit <span x-show="form.is_consumable">*</span></label>
                        <input @click="selectLOV()" type="text" placeholder="UOM" x-model="form.unit_title" :disabled="form.disabled"
                            autocomplete="off" readonly>
                        <span class="error" x-show="validate?.unit_id" x-text="validate?.unit_id"></span>
                    </div>
                    <div class="form-row !flex-row !justify-between m-0 border-b border-[#d8dce5]">
                        <label>Item Variate & Product</label>
                        <span @click="addMoreItemVariate()">
                            <img class="cursor-pointer w-[20px] h-[20px]" src="{!! asset('storage/icons/plus-circle.svg') !!}">
                        </span>
                    </div>
                    <template x-for="(variate, index) in item_variates">
                        <div class="row  border border-[#d8dce5] p-3 rounded mb-[10px] relative">
                            <span @click="onRemoveItemVariate(index)" class="absolute top-[-10px] right-0">
                                <img class="cursor-pointer w-[20px] h-[20px]" src="{!! asset('storage/icons/minus-circle.svg') !!}" alt="">
                            </span>
                            <div class="row-3">
                                <div class="form-row">
                                    <label>Title (EN) </label>
                                    <input type="text" placeholder="ឈុតជ្រូកកណ្ដុរខ្វៃអំបិលម្ទេស ខ្នាតធំ"
                                        x-model="variate.title_en" :disabled="form.disabled" autocomplete="off">
                                    <span class="error" x-show="validate?.['item_variates.' + index + '.title_en']" x-text="validate?.['item_variates.' + index + '.title_en']"></span>
                                </div>
                                <div class="form-row">
                                    <label>Title (KM) </label>
                                    <input type="text" placeholder="ឈុតជ្រូកកណ្ដុរខ្វៃអំបិលម្ទេស ខ្នាតធំ"
                                        x-model="variate.title_km" :disabled="form.disabled" autocomplete="off">
                                    <span class="error" x-show="validate?.['item_variates.' + index + '.title_km']" x-text="validate?.['item_variates.' + index + '.title_km']"></span>
                                </div>
                                <div class="form-row">
                                    <label>Status </label>
                                    <select x-model="variate.status" :disabled="form.disabled">
                                        <option value="ACTIVE">Active</option>
                                        <option value="INACTIVE">Inactive</option>
                                    </select>
                                    <span class="error" x-show="validate?.status" x-text="validate?.status"></span>
                                </div>
                                <div class="row-2" x-show="form.is_sellable">
                                    <div class="form-row">
                                        <div class="form-check">
                                            <label :for="`is_available-${index}`" class="form-check-label select-none !p-0">Available</label>
                                            <input :id="`is_available-${index}`" class="form-check-input" type="checkbox"
                                                x-model="variate.is_available" :disabled="form.disabled" autocomplete="off">
                                            <span class="error" x-show="validate?.is_available"
                                                x-text="validate?.is_available"></span>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-check">
                                            <label :for="`is_note-${index}`" class="form-check-label select-none !p-0">Show Note</label>
                                            <input :id="`is_note-${index}`" class="form-check-input" type="checkbox"
                                                x-model="variate.is_note" :disabled="form.disabled" autocomplete="off">
                                            <span class="error" x-show="validate?.is_note"
                                                x-text="validate?.is_note"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-row">
                                    <label>Image</label>
                                    <input type="file" :disabled="form.disabled" accept="image/*" :id="`image-${index}`"
                                        class="!p-[12px]" @change="onPreviewImage($el, index)">
                                    <input type="hidden" x-model="variate.tmp_file">
                                    <template x-if="variate.image_url">
                                        <div
                                            class="h-[250px] rounded-md border border-gray-100 overflow-hidden relative grid place-items-center group mt-2">
                                            <img class="w-full h-full object-contain" :src="variate.image_url" alt="">
                                            <div class="absolute flex gap-2 opacity-0 group-hover:opacity-100 duration-[0.2s]">
                                                <button type="button"
                                                    class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                                    @click="onViewImage(variate.image_url)">
                                                    <span class="material-icons-outlined text-white text-2xl w-[24px]">
                                                        visibility_on
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row-2" x-show="form.is_sellable">
                                <div class="form-row">
                                    <label>Price <span>*</span></label>
                                    <input type="text" placeholder="149" x-model="variate.price" :disabled="form.disabled" autocomplete="off">
                                    <span class="error" x-show="validate?.['item_variates.' + index + '.price']" x-text="validate?.['item_variates.' + index + '.price']"></span>
                                </div>
                                <div class="form-row">
                                    <label>Size</label>
                                    <input type="text" placeholder="3.3" x-model="variate.size" :disabled="form.disabled" autocomplete="off">
                                    <span class="error" x-show="validate?.size" x-text="validate?.size"></span>
                                </div>
                                <div class="form-row">
                                    <label>Description (EN)</label>
                                    <textarea type="text" placeholder="..."
                                        x-model="variate.description_en" :disabled="form.disabled" autocomplete="off">
                                    </textarea>
                                    <span class="error" x-show="validate?.['item_variates.' + index + '.description_en']" x-text="validate?.['item_variates.' + index + '.description_en']"></span>
                                </div>
                                <div class="form-row">
                                    <label>Description (KM)</label>
                                    <textarea type="text" placeholder="..."
                                        x-model="variate.description_km" :disabled="form.disabled" autocomplete="off">
                                    </textarea>
                                    <span class="error" x-show="validate?.['item_variates.' + index + '.description_km']" x-text="validate?.['item_variates.' + index + '.description_km']"></span>
                                </div>
                                <div class="form-row">
                                    <label>Note (EN)</label>
                                    <input type="text" placeholder="3+ KG" x-model="variate.note_en" :disabled="form.disabled" autocomplete="off">
                                    <span class="error" x-show="validate?.note_en" x-text="validate?.note_en"></span>
                                </div>
                                <div class="form-row">
                                    <label>Note (KM)</label>
                                    <input type="text" placeholder="3 គីឡូក្រាមជាង" x-model="variate.note_km" :disabled="form.disabled" autocomplete="off">
                                    <span class="error" x-show="validate?.note_km" x-text="validate?.note_km"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </section>
                <div class="row">
                    <div class="form-row">
                        <label>Image</label>
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
                    <button type="button" color="primary" @click="onSave('close')" :disabled="form.disabled || loading">
                        <span class="material-icons mr-1">save</span>
                        <span>Save & Close</span>
                        <div class="loader" style="display: none" x-show="loading"></div>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        Alpine.data('storeCopyItemDialog', () => ({
            form: new FormGroup({
                branch_id: ['', ['required']],
                branch_title: ['', ['required']],
                code: ['', []],
                title_en: ['', []],
                title_km: ['', []],
                unit_id: ['', []],
                unit_title: ['', []],
                is_sellable: ['', []],
                is_consumable: ['', []],
                status: ['ACTIVE', []],
                description_en: ['', []],
                description_km: ['', []],
                image: ['', []],
                tmp_file: ['', []],
                type: ["{{$type}}", []],
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
            item_variates: [{
                item_variate_id: '',
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
                this.dialogData = this.$dialog('storeCopyItemDialog').data;
                feather.replace();
                if (this.dialogData?.id) {
                    await this.fetchDataForUpdate(Number(this.dialogData?.id), (res) => {
                        this.data = res?.data;
                    });
                    this.setValue(this.data);
                }
                else{
                    this.form.is_sellable = this.form.type == 'customer' ? 1 : 0;
                    this.form.is_consumable = this.form.type == 'inventory' ? 1 :0;
                }
            },
            async fetchDataForUpdate(id, callback) {
                await Axios({
                    url: `{{ route('admin-item-detail') }}`,
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
                this.item_variates = [];
                this.addMoreItemVariate();
            },
            setValue(data){
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

                if(data.categories && data.categories.length > 0){
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

                if (data?.item_variates) {
                    this.item_variates = [];
                    this.item_variates = data.item_variates.map(item => ({
                        item_variate_id: '',
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
                if(data?.branch){
                    this.form.branch_id = data?.branch?.id;
                    this.form.branch_title = data?.branch?.title?.en;
                }
            },
            onPreviewImage(el, index = null) {
                const profile = URL.createObjectURL(el.files[0]);
                if (index !== null) {
                    this.item_variates[index].image_url = profile;
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
            addMoreItemVariate(){
                this.item_variates.push({
                    item_variate_id: '',
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
            onRemoveItemVariate(index){
                if(this.item_variates[index].item_variate_id){
                    toastr.info("Can't remove Item Variate!", {
                        progressBar: true,
                        timeOut: 5000
                    });
                }else{
                    this.item_variates.splice(index, 1);
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
                        }else{
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
                    url: `{{ route('admin-validation-item') }}`,
                    method: 'POST',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                    data: {
                        ...data,
                        item_variates: this.item_variates,
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
            getImageBinary(){
                if(this.item_variates.length > 0){
                    this.item_variates.forEach((item, index) => {
                        if(!item.image || item.tmp_file == ''){
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
                    if(res.status == 422){
                        toastr.info(res.response.data.message, {
                            progressBar: true,
                            timeOut: 5000
                        });
                    }
                });
                if (!this.validate) {
                    this.$store.confirmDialog.open({
                        data: {
                            title: "Message",
                            message: "Are you sure to save?",
                            btnClose: "Close",
                            btnSave: "Save",
                        },
                        afterClosed: (result) => {
                            if (result) {
                                this.form.disable();
                                this.loading = true;
                                let file = document.querySelector('#image');
                                this.form.image = file.files[0] ?? '';
                                this.getImageBinary();
                                console.log(this.item_variates);

                                const data = this.form.value();

                                Axios({
                                    url: `{{ route('admin-item-save') }}`,
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'multipart/form-data',
                                    },
                                    data: {
                                        ...data,
                                        item_variates: this.item_variates,
                                    }
                                }).then((res) => {
                                    if (res.data.error == false) {
                                        this.form.reset();
                                        this.$dialog('storeCopyItemDialog').close(true);
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
