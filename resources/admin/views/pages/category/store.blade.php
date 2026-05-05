<template x-dialog="storeCategoryDialog">
    <div x-data="storeCategoryDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 x-show="!data" class="text-gray-600">@lang('form.name.create') (@lang('form.name.category'))</h3>
                <h3 x-show="data?.id">@lang('form.header.update', ['name' => __('form.title.category')])</h3>
                <span @click="$dialog('storeCategoryDialog').close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto">
                <div class="row-2">

                    <div class="form-row">
                        <label>@lang('form.body.label.title_en')<span>*</span></label>
                        <input type="text" placeholder="@lang('form.body.placeholder.title_en')" x-model="form.title_en"
                            @input="!data && generateSlug($event.target.value)"
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
                        <label>@lang('form.body.label.description_en')</label>
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
                <div class="row-2">
                    <div class="form-row">
                        <label>Parent Category</label>
                        <input @click="selectParentCategory()" type="text" placeholder="Select parent category"
                            x-model="form.parent_title" :disabled="form.disabled" autocomplete="off" readonly>
                    </div>
                    <div class="form-row">
                        <label>Slug<span>*</span></label>
                        <input type="text" placeholder="category-slug" x-model="form.slug"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.slug" x-text="validate?.slug"></span>
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
            </div>
            <div class="form-footer">
                <div class="form-button">
                    <button type="button" color="primary" @click="onSave('close')"
                        :disabled="form.disabled || loading">
                        <span class="material-icons mr-1">save</span>
                        <span>@lang('form.button.save')</span>
                        <div class="loader" style="display: none" x-show="loading"></div>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        Alpine.data('storeCategoryDialog', () => ({
            form: new FormGroup({
                status: ['ACTIVE', ['required']],
                title_en: ['', ['required']],
                title_km: ['', []],
                slug: ['', ['required']],
                parent_id: ['', []],
                parent_title: ['', []],
            }),
            data: null,
            dialogData: null,
            validate: null,
            loading: false,
            async init() {
                this.dialogData = this.$dialog('storeCategoryDialog').data;
                if (this.dialogData?.id) {
                    await this.fetchDataForUpdate(Number(this.dialogData?.id), (res) => {
                        this.data = res?.data;
                    });
                    this.setValue(this.data);
                }
                feather.replace();
            },
            async fetchDataForUpdate(id, callback) {
                await Axios({
                    url: `{{ route('admin-category-detail') }}`,
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
                this.form.title_en = data?.title?.en;
                this.form.title_km = data?.title?.km;
                this.form.status = data?.status;
                this.form.slug = data?.slug;
                this.form.parent_id = data?.parent_id ?? '';
                this.form.parent_title = data?.parent?.title?.en ?? '';
            },
            generateSlug(value) {
                this.form.slug = value
                    .toLowerCase()
                    .trim()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            },
            selectParentCategory() {
                SelectOption({
                    title: "Select Parent Category",
                    placeholder: "Search ...",
                    multiple: false,
                    selected: this.form.parent_id ? {
                        _id: this.form.parent_id,
                        _title: this.form.parent_title
                    } : null,
                    unselect: true,
                    onReady: (callback_data) => {
                        Axios.get(`{{ route('admin-fetch-category-data') }}`).then((response) => {
                            const data = (response?.data ?? [])
                                .filter(item => item.id !== this.dialogData?.id)
                                .map(item => ({
                                    _id: item.id,
                                    _title: item.title?.en ?? 'Untitled',
                                    _description: item.slug ?? '',
                                }));
                            callback_data(data);
                        });
                    },
                    onSearch: (value, callback_data) => {
                        Axios.get(`{{ route('admin-fetch-category-data') }}`, {
                            params: {
                                search: value,
                            }
                        }).then((response) => {
                            const data = (response?.data ?? [])
                                .filter(item => item.id !== this.dialogData?.id)
                                .map(item => ({
                                    _id: item.id,
                                    _title: item.title?.en ?? 'Untitled',
                                    _description: item.slug ?? '',
                                }));
                            callback_data(data);
                        });
                    },
                    afterClose: (res) => {
                        if (res) {
                            this.form.parent_id = res._id;
                            this.form.parent_title = res._title;
                        } else {
                            this.form.parent_id = '';
                            this.form.parent_title = '';
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
                    url: `{{ route('admin-validation-category') }}`,
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
                                this.form.disable();
                                this.loading = true;
                                const data = this.form.value();
                                Axios({
                                    url: `{{ route('admin-category-save') }}`,
                                    method: 'POST',
                                    data: {
                                        ...data,
                                        id: this.dialogData?.id,
                                    }
                                }).then((res) => {
                                    if (res.data.error == false) {
                                        this.form.reset();
                                        this.$dialog('storeCategoryDialog').close(true);
                                        toastr.success(res.data.message, {
                                            progressBar: true,
                                            timeOut: 5000
                                        });
                                    } else {
                                        toastr.error(res.data.message, {
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
