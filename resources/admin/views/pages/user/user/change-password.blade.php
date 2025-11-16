<template x-dialog="changePasswordDialog">
    <div x-data="changePasswordDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 class="text-gray-600" x-text="'Change Password for user (' + dialogData.name + ')'"></h3>
                <span @click="$dialog('changePasswordDialog').close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto" x-data="{ show_new_password: false, show_confirm_new_password: false }">
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.new_password')<span>*</span> </label>
                        <div class="relative !w-full">
                            <input :type="!show_new_password ? 'password' : 'text'" x-model="form.new_password"
                                :disabled="form.disabled" class="py-3 px-4 pr-11 block w-full focus:z-10"
                                placeholder="@lang('form.body.placeholder.new_password')" autocomplete="off"
                                style="height: 45px; border: 1px solid #d8dce5; border-radius: 4px; font-size: 14px; color: #212529;">
                            <div @click="show_new_password = !show_new_password"
                                class="absolute inset-y-0 right-0 flex items-center z-20 pr-4">
                                <span x-show="show_new_password"
                                    class="material-icons-outlined text-gray-400 cursor-pointer">visibility</span>
                                <span x-show="!show_new_password"
                                    class="material-icons-outlined text-gray-400 cursor-pointer">visibility_off</span>
                            </div>
                            <span class="error" x-show="validate?.new_password" x-text="validate?.new_password"></span>
                        </div>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.password_confirmation')<span>*</span> </label>
                        <div class="relative !w-full">
                            <input :type="!show_confirm_new_password ? 'password' : 'text'"
                                x-model="form.confirm_new_password" :disabled="form.disabled"
                                class="py-3 px-4 pr-11 block w-full focus:z-10" placeholder="@lang('form.body.placeholder.password_confirmation')"
                                autocomplete="off"
                                style="height: 45px; border: 1px solid #d8dce5; border-radius: 4px; font-size: 14px; color: #212529;">
                            <div @click="show_confirm_new_password = !show_confirm_new_password"
                                class="absolute inset-y-0 right-0 flex items-center z-20 pr-4">
                                <span x-show="show_confirm_new_password"
                                    class="material-icons-outlined text-gray-400 cursor-pointer">visibility</span>
                                <span x-show="!show_confirm_new_password"
                                    class="material-icons-outlined text-gray-400 cursor-pointer">visibility_off</span>
                            </div>
                            <span class="error" x-show="validate?.confirm_new_password"
                                x-text="validate?.confirm_new_password"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-footer">
                <div class="form-button">
                    <button type="button" color="primary" @click="onChangePassword()"
                        :disabled="form.disabled || loading">
                        <span class="material-icons mr-1">save</span>
                        <span x-show="dialogData?.id">@lang('form.button.update')</span>
                        <div class="loader" style="display: none" x-show="loading"></div>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        Alpine.data('changePasswordDialog', () => ({
            form: new FormGroup({
                id: [null, []],
                new_password: [null, ['required']],
                confirm_new_password: [null, ['required']],
            }),
            validate: null,
            loading: false,
            dialogData: null,
            init() {
                this.dialogData = this.$dialog('changePasswordDialog').data;
                this.form.patchValue(this.dialogData ?? {});
                feather.replace();
            },
            onChangePassword() {
                this.$store.confirmDialog.open({
                    data: {
                        title: `@lang('dialog.title')`,
                        message: `@lang('dialog.msg.update')?`,
                        btnClose: "@lang('dialog.button.close')",
                        btnSave: "@lang('dialog.button.update')",
                    },
                    afterClosed: (result) => {
                        if (result) {
                            this.loading = true;
                            const data = this.form.value();
                            Axios({
                                url: `{{ route('admin-user-save-password') }}`,
                                method: 'POST',
                                data: {
                                    ...data,
                                    id: this.dialogData?.id,
                                }
                            }).then((res) => {
                                if (res.data.error == false) {
                                    this.form.reset();
                                    this.$dialog('changePasswordDialog').close(true);
                                    Toast({
                                        message: res.data.message,
                                        status: res.data.status,
                                        size: 'small',
                                    });
                                }
                            }).catch((e) => {
                                this.validate = e.response.data.errors;
                            }).finally(() => {
                                this.loading = false;
                            });
                        }
                    }
                });
            },
        }));
    </script>
</template>
