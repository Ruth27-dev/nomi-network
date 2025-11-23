<template x-dialog="storeRoleDialog">
    <div x-data="storeRoleDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 x-show="!dialogData?.id">
                    @lang('form.header.create', ['name' => __('form.title.user_role')])
                </h3>
                <h3 x-show="dialogData?.id">
                    @lang('form.header.update', ['name' => __('form.title.user_role')])
                </h3>
                <span @click="$dialog('storeRoleDialog').close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto" x-data="{ show_password: false, show_confirm_password: false }">
                <div class="row">
                    <div class="form-row">
                        <label>@lang('form.body.label.name_en')<span>*</span></label>
                        <input placeholder="@lang('form.body.placeholder.name_en')" type="text" x-model="form.display_name_en"
                            :disabled="form.disabled" autocomplete="off"
                            :readonly="form.name == 'chef' || form.name == 'operator'">
                        <span class="error" x-show="validate?.display_name_en"
                            x-text="validate?.display_name_en"></span>
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
        Alpine.data('storeRoleDialog', () => ({
            form: new FormGroup({
                display_name_en: [null, ['required']],
                name: [null, ['required']],
                status: [active, ['required']],
            }),
            baseUrl: "{{ asset('storage/user') }}/",
            profile_url: null,
            dialogData: null,
            validate: null,
            loading: false,
            init() {
                this.dialogData = this.$dialog('storeRoleDialog').data;
                if (this.dialogData?.id) {
                    this.form.patchValue(this.dialogData ?? {});
                    this.form.display_name_en = this.dialogData?.display_name?.en;
                    this.form.display_name_km = this.dialogData?.display_name?.km;
                }
                feather.replace();
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
                            const data = this.form.value();
                            Axios({
                                url: `{{ route('admin-user-role-save') }}`,
                                method: 'POST',
                                data: {
                                    ...data,
                                    id: this.dialogData?.id,
                                }
                            }).then((res) => {
                                if (res.data.error == false) {
                                    this.form.reset();
                                    this.$dialog('storeRoleDialog').close(true);
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
        }));
    </script>
</template>
