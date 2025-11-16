@component('admin::components.dialog', ['dialog' => 'profileChangePasswordDialog'])
    <div x-data="changePasswordDialog" class="form-admin" style="width: 400px">
        <form class="form-wrapper" style="max-width: initial">
            <div class="form-header">
                <h3>
                    @lang('form.title.change_password') ( <span x-text="$store.profileChangePasswordDialog.data?.name"></span> )
                </h3>
                <span @click="$store.profileChangePasswordDialog.close()"><i data-feather="x"></i></span>
            </div>
            {{ csrf_field() }}
            <div class="form-body" style="box-shadow: none">
                <div class="form-row">
                    <label>@lang('form.body.label.new_password')<span>*</span> </label>
                    <input x-bind:type="!show ? 'password' : 'text'" placeholder="@lang('form.body.placeholder.new_password')"
                        x-model="form.new_password" maxlength="20" autocomplete="off">
                    <span class="error" x-show="validate?.new_password" x-text="validate?.new_password"></span>
                </div>
                <div class="form-row">
                    <label>@lang('form.body.label.password_confirmation')<span>*</span> </label>
                    <input x-bind:type="!show ? 'password' : 'text'" placeholder="@lang('form.body.placeholder.password_confirmation')"
                        x-model="form.confirm_new_password" maxlength="20" autocomplete="off">
                    <span class="error" x-show="validate?.confirm_new_password"
                        x-text="validate?.confirm_new_password"></span>
                </div>
                <div class="row flex items-center justify-end mb-5">
                    <input @click="show = !show" class="!w-5 h-5 cursor-pointer" type="checkbox" id="show-password">
                    <label for="show-password" class="text-gray-500 text-sm ml-2 cursor-pointer">@lang('form.button.show_password')</label>
                </div>
                <input type="hidden" x-model="form.id">
                <div class="form-button">
                    <button type="button" color="primary" @click="onChangePassword()" :disabled="form.disabled || loading">
                        <i data-feather="save"></i>
                        <span>@lang('form.button.save')</span>
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
                confirm_new_password: [null, []],
            }),
            show: false,
            errors: {},
            validate: {},
            loading: false,
            dialogData: null,
            init() {
                this.dialogData = this.$store.profileChangePasswordDialog.data;
                this.changePasswordForm(this.dialogData);
            },
            changePasswordForm(data) {
                if (!data) return;
                this.form.patchValue({
                    id: data.id,
                });
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
                                data: data
                            }).then((res) => {
                                if (res.data.error == false) {
                                    this.form.reset();
                                    this.$store.profileChangePasswordDialog.close();
                                    Toast({
                                        message: res.data.message,
                                        status: res.data.status,
                                        size: 'small',
                                    });
                                }
                            }).catch((e) => {
                                this.errors = e.response.data.errors;
                                this.validate.new_password = this.errors.new_password;
                                this.validate.confirm_new_password = this.errors
                                    .confirm_new_password;
                            }).finally(() => {
                                this.loading = false;
                            });
                        }
                    }
                });
            },
        }));
    </script>
@endcomponent
