<template x-dialog="storeUserDialog">
    <div x-data="storeUserDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 x-show="!dialogData?.id">
                    @lang('form.header.create', ['name' => __('form.title.user')])
                </h3>
                <h3 x-show="dialogData?.id">
                    @lang('form.header.update', ['name' => __('form.title.user')])
                </h3>
                <span @click="$dialog('storeUserDialog').close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto" x-data="{ show_password: false, show_confirm_password: false }">
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.name')<span>*</span> </label>
                        <input type="text" placeholder="@lang('form.body.placeholder.name')" x-model="form.name"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.name" x-text="validate?.name"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.email') <span>*</span></label>
                        <input type="email" placeholder="@lang('form.body.placeholder.email')" x-model="form.email"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.email" x-text="validate?.email"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.phone')<span>*</span> </label>
                        <input type="number" placeholder="@lang('form.body.placeholder.phone')" x-model="form.phone"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.phone" x-text="validate?.phone"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.role')<span>*</span> </label>
                        <select x-model="form.role_id" :disabled="form.disabled">
                            <option value="">@lang('form.body.placeholder.role')</option>
                            <template x-for="(item,index) in roleData">
                                <option :value="item?.id" :selected="item?.id == form.role_id"><span
                                        x-text="item?.display_name?.[langLocale]"></span></option>
                            </template>
                        </select>
                        <span class="error" x-show="validate?.role_id" x-text="validate?.role_id"></span>
                    </div>
                    <div class="form-row" x-show="!dialogData?.id">
                        <label>@lang('form.body.label.password')<span>*</span> </label>
                        <div class="relative !w-full">
                            <input :type="!show_password ? 'password' : 'text'" x-model="form.password"
                                :disabled="form.disabled" class="py-3 px-4 pr-11 block w-full focus:z-10"
                                placeholder="Enter password" autocomplete="off"
                                style="height: 45px; border: 1px solid #d8dce5; border-radius: 4px; font-size: 14px; color: #212529;">
                            <div @click="show_password = !show_password"
                                class="absolute inset-y-0 right-0 flex items-center z-20 pr-4">
                                <span x-show="show_password"
                                    class="material-icons-outlined text-gray-400 cursor-pointer">visibility</span>
                                <span x-show="!show_password"
                                    class="material-icons-outlined text-gray-400 cursor-pointer">visibility_off</span>
                            </div>
                            <span class="error" x-show="validate?.password" x-text="validate?.password"></span>
                        </div>
                    </div>
                    <div class="form-row" x-show="!dialogData?.id">
                        <label>@lang('form.body.label.password_confirmation')<span>*</span></label>
                        <div class="relative !w-full">
                            <input :type="!show_confirm_password ? 'password' : 'text'"
                                x-model="form.password_confirmation" :disabled="form.disabled"
                                class="py-3 px-4 pr-11 block w-full focus:z-10" placeholder="@lang('form.body.placeholder.password_confirmation')"
                                autocomplete="off"
                                style="height: 45px; border: 1px solid #d8dce5; border-radius: 4px; font-size: 14px; color: #212529;">
                            <div @click="show_confirm_password = !show_confirm_password"
                                class="absolute inset-y-0 right-0 flex items-center z-20 pr-4">
                                <span x-show="show_confirm_password"
                                    class="material-icons-outlined text-gray-400 cursor-pointer">visibility</span>
                                <span x-show="!show_confirm_password"
                                    class="material-icons-outlined text-gray-400 cursor-pointer">visibility_off</span>
                            </div>
                            <span class="error" x-show="validate?.password_confirmation"
                                x-text="validate?.password_confirmation"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-row">
                        <label>@lang('form.body.label.address')</label>
                        <textarea x-model="form.address" name="" id="" rows="1" placeholder="@lang('form.body.placeholder.address')"></textarea>
                    </div>
                    <span class="error" x-show="validate?.address" x-text="validate?.address"></span>
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
                        <label>@lang('form.body.label.profile')</label>
                        <input type="file" :disabled="form.disabled" accept="image/*" id="profile"
                            class="!p-[12px]" @change="onPreviewProfile($el)">
                        <input type="hidden" x-model="form.tmp_file">
                        <template x-if="profile_url">
                            <div
                                class="h-[250px] rounded-md border border-gray-100 overflow-hidden relative grid place-items-center group mt-2">
                                <img class="w-full h-full object-contain" :src="profile_url" alt="">
                                <div class="absolute flex gap-2 opacity-0 group-hover:opacity-100 duration-[0.2s]">
                                    <button type="button"
                                        class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                        @click="onViewProfile(profile_url)">
                                        <span class="material-icons-outlined text-white text-2xl w-[24px]">
                                            visibility_on
                                        </span>
                                    </button>
                                    <button type="button"
                                        class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                        @click="onRemoveProfile()">
                                        <span class="material-icons-outlined text-white text-2xl w-[24px]">
                                            delete
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
        Alpine.data('storeUserDialog', () => ({
            form: new FormGroup({
                name: [null, ['required']],
                email: [null, ['required']],
                phone: [null, ['required']],
                role: [null, ['required']],
                role_id: [null, ['required']],
                status: [active, ['required']],
                address: [null, ['required']],
                password: [null, ['required']],
                password_confirmation: [null, ['required']],
                profile: [null, []],
                tmp_file: [null, []],
            }),
            profile_url: null,
            dialogData: null,
            validate: null,
            loading: false,
            selected_branch: [],
            roleData: @json($roles),
            init() {
                this.dialogData = this.$dialog('storeUserDialog').data;
                this.form.patchValue(this.dialogData ?? {});
                feather.replace();
                this.form.default_branch_title = this.dialogData?.branch_default ? this.dialogData
                    ?.branch_default?.title?.en : null;
                this.form.default_branch_id = this.dialogData?.branch_id;
                if (this.dialogData.branches && this.dialogData.branches.length > 0) {
                    this.selected_branch = this.dialogData.branches.map(item => {
                        return {
                            _id: item?.id,
                            _title: item?.title?.en,
                            _description: item?.description?.en,
                        }
                    });
                    this.form.branch_ids = this.selected_branch.map(item => item._id);
                    this.form.branch_title = this.selected_branch.map(item => item._title).join(', ');
                }
                this.profile_url = this?.dialogData?.profile_url ? this.dialogData?.profile_url : null;
            },
            onPreviewProfile(el) {
                const profile = URL.createObjectURL(el.files[0]);
                this.profile_url = profile;
            },
            onViewProfile(path) {
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
            onRemoveProfile() {
                this.form.tmp_file = null;
                this.profile_url = null;
                document.querySelector('#profile').value = '';
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
                            let file = document.querySelector('#profile');
                            this.form.profile = file.files[0];
                            const data = this.form.value();
                            Axios({
                                url: `{{ route('admin-user-save') }}`,
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
                                    this.$dialog('storeUserDialog').close(true);
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
