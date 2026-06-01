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
                        <label>@lang('form.body.label.image')</label>
                        <input type="file" accept="image/*" style="display:none" x-ref="mvImageInput"
                            :disabled="form.disabled" @change="onAddImage($event)">
                        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;">
                            <template x-for="(img, imgIdx) in images" :key="imgIdx">
                                <div style="position:relative;width:80px;height:80px;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;flex-shrink:0;"
                                    @mouseenter="$el.querySelector('.img-actions').style.opacity='1'"
                                    @mouseleave="$el.querySelector('.img-actions').style.opacity='0'">
                                    <img style="width:100%;height:100%;object-fit:cover;" :src="img.url" alt="">
                                    <div class="img-actions" style="position:absolute;inset:0;background:rgba(0,0,0,0.45);display:flex;align-items:center;justify-content:center;gap:4px;opacity:0;transition:opacity 0.2s;">
                                        <button type="button"
                                            style="width:28px;height:28px;background:#fff;border-radius:50%;border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.2);"
                                            @click="onViewImage(img.url)">
                                            <span class="material-icons" style="font-size:15px;color:#374151;">visibility</span>
                                        </button>
                                        <button type="button"
                                            style="width:28px;height:28px;background:#fff;border-radius:50%;border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.2);"
                                            @click="removeImage(imgIdx)">
                                            <span class="material-icons" style="font-size:15px;color:#ef4444;">delete</span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <button type="button" :disabled="form.disabled"
                                style="width:80px;height:80px;border-radius:8px;border:2px dashed #d1d5db;background:transparent;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;cursor:pointer;flex-shrink:0;transition:border-color 0.2s,background 0.2s;"
                                @mouseenter="$el.style.borderColor='#60a5fa';$el.style.background='#eff6ff'"
                                @mouseleave="$el.style.borderColor='#d1d5db';$el.style.background='transparent'"
                                @click="$refs.mvImageInput.click()">
                                <span class="material-icons" style="font-size:22px;color:#9ca3af;">add</span>
                                <span style="font-size:11px;color:#9ca3af;">Add</span>
                            </button>
                        </div>
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
            }),
            images: [],
            baseLegacyUrl: "{{ asset('storage/list-of-value') }}/",
            baseUploadUrl: "{{ asset('uploads') }}/",
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
                    const existingPaths = this.dialogData?.add_on?.images
                        || (this.dialogData?.image ? [this.dialogData.image] : []);
                    this.images = existingPaths.map(path => ({
                        file: null,
                        tmp: path,
                        url: this.resolveImageUrl(path),
                    }));
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
            resolveImageUrl(file) {
                if (!file) return null;
                if (file instanceof File) return URL.createObjectURL(file);
                if (file.startsWith('http') || file.startsWith('blob:')) return file;
                const normalized = file.replace(/^\/+/, '');
                if (normalized.includes('/')) {
                    return this.baseUploadUrl + normalized.replace(/^uploads\//, '');
                }
                return this.baseLegacyUrl + normalized;
            },
            onAddImage(event) {
                const file = event.target.files[0];
                if (!file) return;
                this.images.push({ file, url: URL.createObjectURL(file), tmp: null });
                event.target.value = '';
            },
            removeImage(index) {
                this.images.splice(index, 1);
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

                            const formData = new FormData();
                            const data = this.form.value();
                            for (const key in data) {
                                if (data[key] !== null && data[key] !== undefined && data[key] !== '') {
                                    formData.append(key, data[key]);
                                }
                            }
                            if (this.dialogData?.id) {
                                formData.append('id', this.dialogData.id);
                            }
                            let newIdx = 0, tmpIdx = 0;
                            this.images.forEach(img => {
                                if (img.file instanceof File) {
                                    formData.append(`images[${newIdx++}]`, img.file);
                                } else if (img.tmp) {
                                    formData.append(`tmp_images[${tmpIdx++}]`, img.tmp);
                                }
                            });

                            Axios.post(`{{ route('admin-page-mission-vision-save') }}`, formData, {
                                headers: { 'Content-Type': 'multipart/form-data' },
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
