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
                        <label for="status">@lang('form.body.label.status')<span>*</span> </label>
                        <select id="status" x-model="form.status" :disabled="form.disabled">
                            <option value="ACTIVE">Active</option>
                            <option value="INACTIVE">Inactive</option>
                        </select>
                        <span class="error" x-show="validate?.status" x-text="validate?.status"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.ordering')<span>*</span></label>
                        <input placeholder="@lang('form.body.placeholder.ordering')" type="number" x-model="form.sequence"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.sequence" x-text="validate?.sequence"></span>
                    </div>
                </div>
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
                title_km: ['', ['required']],
                description_en: ['', ['required']],
                description_km: ['', ['required']],
                image: ['', ['required']],
                sequence: ['', ['required']],
                tmp_file: ['', ['required']],
                slug: ['', ['required']],
            }),
            data: null,
            dialogData: null,
            validate: null,
            loading: false,
            image_url: null,
            async init() {
                this.dialogData = this.$dialog('storeCategoryDialog').data;
                if (this.dialogData?.id) {
                    await this.fetchDataForUpdate(Number(this.dialogData?.id), (res) => {
                        this.data = res?.data;
                    });
                    this.setValue(this.data);
                } else {
                    await this.getMaxOrdering((res) => {
                        console.log(res);

                        this.form.sequence = res.max_ordering;
                    });
                }
                feather.replace();
            },
            async getMaxOrdering(callback) {
                await Axios({
                    url: `{{ route('admin-category-sequence') }}`,
                    method: 'GET',
                    params: {}
                }).then((res) => {
                    callback(res.data)
                }).catch((e) => {
                    console.log(e);
                });
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
                this.form.description_en = data?.description?.en;
                this.form.description_km = data?.description?.km;
                this.form.slug = data?.slug;
                this.form.sequence = data?.sequence;

                if (data?.image) {
                    this.form.tmp_file = data?.image;
                    this.image_url = this.data?.image_url;
                }
            },
            onPreviewImage(el, index = null) {
                const image = URL.createObjectURL(el.files[0]);
                if (index !== null) {
                    this.item_variates[index].image_url = image;
                    return;
                }
                this.image_url = image;
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
                                const data = this.form.value();
                                Axios({
                                    url: `{{ route('admin-category-save') }}`,
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
                                        this.$dialog('storeCategoryDialog').close(true);
                                    }
                                    toastr.success(res.data.message, {
                                        progressBar: true,
                                        timeOut: 5000
                                    });
                                }).catch((e) => {
                                    this.validate = e.response.data.errors;
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
