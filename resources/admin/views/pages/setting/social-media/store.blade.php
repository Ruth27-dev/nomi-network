<template x-dialog="storeSocialMediaDialog">
    <div x-data="storeSocialMediaDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 x-show="!dialogData?.id">
                    @lang('form.header.create', ['name' => __('form.title.social_media')])
                </h3>
                <h3 x-show="dialogData?.id">
                    @lang('form.header.update', ['name' => __('form.title.social_media')])
                </h3>
                <span @click="close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto">
                <div class="form-header mb-0 !text-sm !flex !justify-end">
                    @include('admin::components.form-change-language')
                </div>
                <div class="form-row" x-show="locale == arrayLangLocale.en">
                    <label>@lang('form.body.label.title_en') <span>*</span></label>
                    <input type="text" placeholder="@lang('form.body.placeholder.title_en')" x-model="form.title_en"
                        :disabled="form.disabled" autocomplete="off">
                    <span class="error" x-show="validate?.title_en" x-text="validate?.title_en"></span>
                </div>
                <div class="form-row" x-show="locale == arrayLangLocale.km">
                    <label>@lang('form.body.label.title_km') <span>*</span></label>
                    <input type="text" placeholder="@lang('form.body.placeholder.title_km')" x-model="form.title_km"
                        :disabled="form.disabled" autocomplete="off">
                    <span class="error" x-show="validate?.title_km" x-text="validate?.title_km"></span>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.ordering')<span>*</span></label>
                        <input id="ordering" placeholder="@lang('form.body.placeholder.ordering')" type="text" x-model="form.ordering"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.ordering" x-text="validate?.ordering"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.status')<span>*</span> </label>
                        <select x-model="form.status" :disabled="form.disabled">
                            @foreach (config('dummy.status') as $key => $status)
                                <option value="{{ $status['key'] }}">{{ $status['text'] }}</option>
                            @endforeach
                        </select>
                        <span class="error" x-show="validate?.status" x-text="validate?.status"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-row">
                        <label>@lang('form.body.label.link') </label>
                        <textarea rows="3" x-model="form.url" placeholder="@lang('form.body.placeholder.link')"></textarea>
                        <span class="error" x-show="validate?.url" x-text="validate?.url"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-row">
                        <label>@lang('form.body.label.image') <span>*</span></label>
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
                                    <button type="button"
                                        class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                        @click="onRemoveImage()">
                                        <span class="material-icons-outlined text-white text-2xl w-[24px]">
                                            delete
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <span class="error" x-show="validate?.image" x-text="validate?.image"></span>
                    </div>
                </div>
            </div>
            <div class="form-footer">
                <div class="form-button">
                    <button type="button" color="primary" @click="onSave()" :disabled="form.disabled || loading">
                        <span class="material-icons mr-1">save</span>
                        <span x-show="!dialogData?.id">@lang('form.button.save')</span>
                        <span x-show="dialogData?.id">@lang('form.button.update')</span>
                        <div class="loader" style="display: none" x-show="loading"></div>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        Alpine.data('storeSocialMediaDialog', () => ({
            locale: @json(config('dummy.locale.en')),
            form: new FormGroup({
                title_en: [null, ['required']],
                title_km: [null, ['required']],
                ordering: [null, ['required']],
                status: ['ACTIVE', ['required']],
                image: [null, []],
                tmp_file: [null, []],
                url: [null, []],
            }),
            image_url: null,
            dialogData: null,
            validate: null,
            loading: false,
            async init() {
                this.dialogData = this.$dialog('storeSocialMediaDialog').data;
                if (this.dialogData.id) {
                    this.form.patchValue(this.dialogData ?? {});
                    this.image_url = this?.dialogData?.image_url ? this.dialogData?.image_url : null;
                    this.form.tmp_file = this?.dialogData?.image ? this.dialogData?.image : null;
                    this.form.title_en = this.dialogData?.title?.en;
                    this.form.title_km = this.dialogData?.title?.km;
                } else {
                    await this.getMaxOrdering((res) => {
                        this.form.ordering = res.max_ordering;
                    });
                }
                feather.replace();
            },
            async getMaxOrdering(callback) {
                await Axios({
                    url: `{{ route('admin-page-social-media-max-ordering') }}`,
                    method: 'GET',
                    params: {}
                }).then((res) => {
                    callback(res.data)
                }).catch((e) => {
                    console.log(e);
                });
            },
            onPreviewImage(el) {
                if (!el.files[0]) return;
                const image = URL.createObjectURL(el.files[0]);
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
            onRemoveImage() {
                this.form.tmp_file = null;
                this.image_url = null;
                document.querySelector('#image').value = '';
            },
            onSave() {
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
                            this.form.image = file.files[0];
                            const data = this.form.value();
                            Axios({
                                url: `{{ route('admin-page-social-media-save') }}`,
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
                                    this.$dialog('storeSocialMediaDialog').close(true);
                                }
                                Toast({
                                    message: res.data.message,
                                    status: res.data.status,
                                    size: 'small',
                                });
                            }).catch((e) => {
                                this.validate = e.response?.data?.errors;
                                if (this.validate) {
                                    const enErrors = Object.keys(this.validate).filter(k => k.includes('_en'));
                                    const kmErrors = Object.keys(this.validate).filter(k => k.includes('_km'));
                                    if (enErrors.length > 0) this.locale = arrayLangLocale.en;
                                    else if (kmErrors.length > 0) this.locale = arrayLangLocale.km;
                                }
                            }).finally(() => {
                                this.form.enable();
                                this.loading = false;
                            });
                        }
                    }
                });
            },
            close() {
                this.$dialog('storeSocialMediaDialog').close();
            }
        }));
    </script>
</template>
