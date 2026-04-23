<template x-dialog="storeAchievementSummaryDialog">
    <div x-data="storeAchievementSummaryDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 x-show="!dialogData?.id">
                    @lang('form.header.create', ['name' => __('form.title.achievement_summary')])
                </h3>
                <h3 x-show="dialogData?.id">
                    @lang('form.header.update', ['name' => __('form.title.achievement_summary')])
                </h3>
                <span @click="close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto">
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.achievement_number')<span>*</span></label>
                        <input placeholder="@lang('form.body.placeholder.achievement_number')" type="text" x-model="form.number"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.number" x-text="validate?.number"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.ordering')<span>*</span></label>
                        <input id="sequence" placeholder="@lang('form.body.placeholder.ordering')" type="number"
                            x-model="form.sequence" :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.sequence" x-text="validate?.sequence"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.title_en')<span>*</span></label>
                        <input placeholder="@lang('form.body.placeholder.title_en')" type="text" x-model="form.title_en"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.title_en" x-text="validate?.title_en"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.title_km')</label>
                        <input placeholder="@lang('form.body.placeholder.title_km')" type="text" x-model="form.title_km"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.title_km" x-text="validate?.title_km"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.description_en')</label>
                        <textarea type="text" placeholder="@lang('form.body.placeholder.description_en')" x-model="form.description_en"
                            :disabled="form.disabled" autocomplete="off" rows="5"></textarea>
                        <span class="error" x-show="validate?.description_en" x-text="validate?.description_en"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.description_km')</label>
                        <textarea type="text" placeholder="@lang('form.body.placeholder.description_km')" x-model="form.description_km"
                            :disabled="form.disabled" autocomplete="off" rows="5"></textarea>
                        <span class="error" x-show="validate?.description_km" x-text="validate?.description_km"></span>
                    </div>
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
                        <label>@lang('form.body.label.icon')<span>*</span></label>
                        <input type="file" :disabled="form.disabled" accept="image/*" id="achievement_icon"
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
        Alpine.data('storeAchievementSummaryDialog', () => ({
            form: new FormGroup({
                number: [null, ['required']],
                title_en: [null, ['required']],
                title_km: [null, []],
                description_en: [null, []],
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
                this.dialogData = this.$dialog('storeAchievementSummaryDialog').data;
                if (this.dialogData?.id) {
                    this.form.patchValue(this.dialogData ?? {});
                    this.form.number = this.dialogData?.code ?? null;
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
            },
            async getMaxOrdering(callback) {
                await Axios({
                    url: `{{ route('admin-page-achievement-summary-max-ordering') }}`,
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
                document.querySelector('#achievement_icon').value = '';
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
                            let file = document.querySelector('#achievement_icon');
                            this.form.image = file.files[0] ?? '';
                            const data = this.form.value();
                            Axios({
                                url: `{{ route('admin-page-achievement-summary-save') }}`,
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
                                    this.$dialog('storeAchievementSummaryDialog').close(true);
                                }
                                Toast({
                                    message: res.data.message,
                                    status: res.data.status,
                                    size: 'small',
                                });
                            }).catch((e) => {
                                this.validate = e.response?.data?.errors;
                            }).finally(() => {
                                this.form.enable();
                                this.loading = false;
                            });
                        }
                    }
                });
            },
            close() {
                this.$dialog('storeAchievementSummaryDialog').close();
            }
        }));
    </script>
</template>
