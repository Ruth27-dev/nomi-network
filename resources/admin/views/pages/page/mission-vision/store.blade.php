<template x-dialog="storeMissionVisionDialog">
    <div x-data="storeMissionVisionDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 x-show="!dialogData?.id">
                    @lang('form.header.create', ['name' => __('form.title.mission_vision')])
                </h3>
                <h3 x-show="dialogData?.id">
                    @lang('form.header.update', ['name' => __('form.title.mission_vision')])
                </h3>
                <span @click="close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto">
                <div class="form-header mb-0 !text-sm !flex !justify-end">
                    @include('admin::components.form-change-language')
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.type')<span>*</span></label>
                        <select x-model="form.type" :disabled="form.disabled">
                            <option value="MISSION">Mission</option>
                            <option value="VISION">Vision</option>
                        </select>
                        <span class="error" x-show="validate?.type" x-text="validate?.type"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.ordering')<span>*</span></label>
                        <input id="mission_vision_sequence" placeholder="@lang('form.body.placeholder.ordering')" type="number"
                            x-model="form.sequence" :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.sequence" x-text="validate?.sequence"></span>
                    </div>
                </div>
                <div class="form-row" x-show="locale == arrayLangLocale.en">
                    <label>@lang('form.body.label.title_en')<span>*</span></label>
                    <input placeholder="@lang('form.body.placeholder.title_en')" type="text" x-model="form.title_en"
                        :disabled="form.disabled" autocomplete="off">
                    <span class="error" x-show="validate?.title_en" x-text="validate?.title_en"></span>
                </div>
                <div class="form-row" x-show="locale == arrayLangLocale.km">
                    <label>@lang('form.body.label.title_km')</label>
                    <input placeholder="@lang('form.body.placeholder.title_km')" type="text" x-model="form.title_km"
                        :disabled="form.disabled" autocomplete="off">
                    <span class="error" x-show="validate?.title_km" x-text="validate?.title_km"></span>
                </div>
                <div class="form-row" x-show="locale == arrayLangLocale.en">
                    <label>@lang('form.body.label.description_en')<span>*</span></label>
                    <textarea id="mv-desc-en" type="text" placeholder="@lang('form.body.placeholder.description_en')" x-model="form.description_en"
                        :disabled="form.disabled" autocomplete="off" rows="5"></textarea>
                    <span class="error" x-show="validate?.description_en" x-text="validate?.description_en"></span>
                </div>
                <div class="form-row" x-show="locale == arrayLangLocale.km">
                    <label>@lang('form.body.label.description_km')</label>
                    <textarea id="mv-desc-km" type="text" placeholder="@lang('form.body.placeholder.description_km')" x-model="form.description_km"
                        :disabled="form.disabled" autocomplete="off" rows="5"></textarea>
                    <span class="error" x-show="validate?.description_km" x-text="validate?.description_km"></span>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.status')<span>*</span> </label>
                        <select x-model="form.status" :disabled="form.disabled">
                            @foreach (config('dummy.status') as $key => $status)
                                <option value="{{ $status['key'] }}">{{ $status['text'] }}</option>
                            @endforeach
                        </select>
                        <span class="error" x-show="validate?.status" x-text="validate?.status"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.image')<span>*</span></label>
                        <input type="file" :disabled="form.disabled" accept="image/*" id="mission_vision_image"
                            class="!p-[12px]" @change="onPreviewImage($el)">
                        <input type="hidden" x-model="form.tmp_file">
                        <span class="error" x-show="validate?.image" x-text="validate?.image"></span>
                    </div>
                </div>
                <template x-if="image_url">
                    <div class="row">
                        <div
                            class="h-[220px] rounded-md border border-gray-100 overflow-hidden relative grid place-items-center group mt-2">
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
                    </div>
                </template>
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
        Alpine.data('storeMissionVisionDialog', () => ({
            locale: @json(config('dummy.locale.en')),
            form: new FormGroup({
                type: ['MISSION', ['required']],
                title_en: [null, ['required']],
                title_km: [null, []],
                description_en: [null, ['required']],
                description_km: [null, []],
                sequence: [null, ['required']],
                status: ['ACTIVE', ['required']],
                image: [null, []],
                tmp_file: [null, []],
            }),
            image_url: null,
            dialogData: null,
            validate: null,
            loading: false,
            async init() {
                this.dialogData = this.$dialog('storeMissionVisionDialog').data;
                if (this.dialogData?.id) {
                    this.form.patchValue(this.dialogData ?? {});
                    this.form.type = this.dialogData?.add_on?.type ?? 'MISSION';
                    this.form.title_en = this.dialogData?.title?.en ?? null;
                    this.form.title_km = this.dialogData?.title?.km ?? null;
                    this.form.description_en = this.dialogData?.description?.en ?? null;
                    this.form.description_km = this.dialogData?.description?.km ?? null;
                    this.form.sequence = this.dialogData?.sequence ?? null;
                    this.form.status = this.dialogData?.status ?? 'ACTIVE';
                    this.form.tmp_file = this.dialogData?.image ?? null;
                    this.image_url = this.dialogData?.image_url ?? null;
                } else {
                    await this.getMaxOrdering((res) => {
                        this.form.sequence = res.max_ordering;
                    });
                }
                feather.replace();
                await this.$nextTick();
                await this.initTinymce();
            },
            async getMaxOrdering(callback) {
                await Axios({
                    url: `{{ route('admin-page-mission-vision-max-ordering') }}`,
                    method: 'GET',
                    params: {}
                }).then((res) => {
                    callback(res.data)
                }).catch((e) => {
                    console.log(e);
                });
            },
            async initTinymce() {
                tinymce.remove('#mv-desc-en, #mv-desc-km');
                await tinymce.init({
                    relative_urls: false,
                    selector: 'textarea#mv-desc-en,textarea#mv-desc-km',
                    height: 400,
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
            },
            onPreviewImage(el) {
                if (!el.files[0]) return;
                this.image_url = URL.createObjectURL(el.files[0]);
            },
            onViewImage(path) {
                Fancybox.show([{
                    src: path,
                    type: "image",
                }, ], {
                    on: {
                        ready: () => {
                            document.querySelector('.fancybox__container').style.zIndex = this
                                .$store.libs.getLastIndex() + 1;
                        },
                    }
                });
            },
            onRemoveImage() {
                this.form.tmp_file = null;
                this.image_url = null;
                document.querySelector('#mission_vision_image').value = '';
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
                            this.form.description_en = tinymce.get('mv-desc-en')?.getContent() ?? '';
                            this.form.description_km = tinymce.get('mv-desc-km')?.getContent() ?? '';
                            this.form.disable();
                            this.loading = true;
                            let file = document.querySelector('#mission_vision_image');
                            this.form.image = file.files[0] ?? '';
                            const data = this.form.value();
                            Axios({
                                url: `{{ route('admin-page-mission-vision-save') }}`,
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
                                    this.$dialog('storeMissionVisionDialog').close(true);
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
                tinymce.remove('#mv-desc-en, #mv-desc-km');
                this.$dialog('storeMissionVisionDialog').close();
            }
        }));
    </script>
</template>
