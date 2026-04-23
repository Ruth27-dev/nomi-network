<template x-dialog="storeBankAccountDialog">
    <div x-data="storeBankAccountDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 x-show="!dialogData?.id">
                    @lang('form.header.create', ['name' => __('form.title.bank_account')])
                </h3>
                <h3 x-show="dialogData?.id">
                    @lang('form.header.update', ['name' => __('form.title.bank_account')])
                </h3>
                <span @click="close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto">
                <div class="row">
                    <div class="form-row">
                        <label>@lang('form.body.label.bank_name')<span>*</span></label>
                        <input placeholder="@lang('form.body.placeholder.bank_name')" type="text" inputmode="numeric"
                            x-model="form.bank_name" :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.bank_name" x-text="validate?.bank_name"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.account_name')<span>*</span></label>
                        <input placeholder="@lang('form.body.placeholder.account_name')" type="text" inputmode="numeric"
                            x-model="form.account_name" :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.account_name" x-text="validate?.account_name"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.account_number')<span>*</span></label>
                        <input placeholder="@lang('form.body.placeholder.account_number')" type="text" inputmode="numeric"
                            x-model="form.bank_number" :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.bank_number" x-text="validate?.bank_number"></span>
                    </div>
                </div>

                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.ordering')<span>*</span></label>
                        <input id="ordering" placeholder="@lang('form.body.placeholder.ordering')" type="number" x-model="form.ordering"
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
                        <label>@lang('form.body.label.qr_code')</label>
                        <input type="file" :disabled="form.disabled" accept="image/*" id="qr_code"
                            class="!p-[12px]" @change="onPreviewQrCode($el)">
                        <input type="hidden" x-model="form.tmp_file">
                        <template x-if="qr_code_url">
                            <div
                                class="h-[250px] rounded-md border border-gray-100 overflow-hidden relative grid place-items-center group mt-2">
                                <img class="w-full h-full object-contain" :src="qr_code_url" alt="">
                                <div class="absolute flex gap-2 opacity-0 group-hover:opacity-100 duration-[0.2s]">
                                    <button type="button"
                                        class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                        @click="onViewQrCode(qr_code_url)">
                                        <span class="material-icons-outlined text-white text-2xl w-[24px]">
                                            visibility_on
                                        </span>
                                    </button>
                                    <button type="button"
                                        class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                        @click="onRemoveQrCode()">
                                        <span class="material-icons-outlined text-white text-2xl w-[24px]">
                                            delete
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <span class="error" x-show="validate?.qr_code" x-text="validate?.qr_code"></span>
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
        Alpine.data('storeBankAccountDialog', () => ({
            form: new FormGroup({
                branch_id: ['', ['required']],
                branch_title: ['', ['required']],
                bank_name: [null, ['required']],
                bank_number: [null, ['required']],
                account_name: [null, ['required']],
                ordering: [null, ['required']],
                status: ['ACTIVE', ['required']],
                qr_code: [null, []],
                tmp_file: [null, []],
            }),
            qr_code_url: null,
            dialogData: null,
            validate: null,
            loading: false,
            async init() {
                this.dialogData = this.$dialog('storeBankAccountDialog').data;
                if (this.dialogData.id) {
                    this.form.patchValue(this.dialogData ?? {});
                    this.qr_code_url = this?.dialogData?.qr_code_url ? this.dialogData?.qr_code_url : null;
                    this.form.branch_id = this.dialogData?.branch?.id;
                    this.form.branch_title = getItemByLang(JSON.stringify(this.dialogData?.branch
                        ?.title), langLocale, arrayLangLocale);
                } else {
                    await this.getMaxOrdering((res) => {
                        this.form.ordering = res.max_ordering;
                    });
                }
                feather.replace();
            },
            async getMaxOrdering(callback) {
                await Axios({
                    url: `{{ route('admin-setting-bank-account-max-ordering') }}`,
                    method: 'GET',
                    params: {}
                }).then((res) => {
                    callback(res.data)
                }).catch((e) => {
                    console.log(e);
                });
            },
            onPreviewQrCode(el) {
                const qr_code = URL.createObjectURL(el.files[0]);
                this.qr_code_url = qr_code;
            },
            onViewQrCode(path) {
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
            onRemoveQrCode() {
                this.form.tmp_file = null;
                this.qr_code_url = null;
                document.querySelector('#qr_code').value = '';
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
                            let file = document.querySelector('#qr_code');
                            this.form.qr_code = file.files[0];
                            const data = this.form.value();
                            Axios({
                                url: `{{ route('admin-setting-bank-account-save') }}`,
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
                                    this.$dialog('storeBankAccountDialog').close(true);
                                }
                                Toast({
                                    message: res.data.message,
                                    status: res.data.status,
                                    size: 'small',
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
            },
            close() {
                this.$dialog('storeBankAccountDialog').close();
            }
        }));
    </script>
</template>
