@component('admin::components.dialog', ['dialog' => 'profileStoreDialog'])
    <div x-data="storeDialog" class="form-admin" style="width: 900px">
        <form class="form-wrapper" style="max-width: initial">
            <div class="form-header">

                <h3>
                    @lang('form.title.update_profile')
                </h3>
                <span @click="$store.profileStoreDialog.close()"><i data-feather="x"></i></span>
            </div>
            {{ csrf_field() }}
            <div class="form-body" style="box-shadow: none">
                <div class="row-3">
                    <div class="form-row">
                        <label>@lang('form.body.label.name') <span>*</span></label>
                        <input placeholder="@lang('form.body.placeholder.name')" type="text" x-model="form.name" :disabled="form.disabled"
                            autocomplete="off">
                        <span class="error" x-show="validate?.name" x-text="validate?.name"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.email')<span>*</span></label>
                        <input type="text" id="email" placeholder="@lang('form.body.placeholder.email')" x-model="form.email"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.email" x-text="validate?.email"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.phone')<span>*</span></label>
                        <input type="text" placeholder="@lang('form.body.placeholder.phone')" min="8" id="phone"
                            inputmode="numeric" x-model="form.phone"maxlength="20" autocomplete="off">
                        <span class="error" x-show="validate?.phone" x-text="validate?.phone"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.address')</label>
                        <textarea x-model="form.address" cols="1" :disabled="form.disabled" autocomplete="off"
                            placeholder="@lang('form.body.placeholder.address')" style="min-height: 300px;"></textarea>
                        <span class="error" x-show="validate?.address" x-text="validate?.address"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.profile')</label>
                        <input type="file" :disabled="form.disabled" accept="image/*" id="profile" class="!p-[12px]"
                            @change="onPreviewProfile($el)" placeholder="@lang('form.body.placeholder.profile')">
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
                <div class="form-button">
                    <button type="button" color="primary" @click="onSave()" :disabled="form.disabled || loading">
                        <i data-feather="save"></i>
                        <span>@lang('form.button.save')</span>
                        <div class="loader" style="display: none" x-show="loading"></div>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        Alpine.data('storeDialog', () => ({
            form: new FormGroup({
                name: [null, ['required']],
                email: [null, ['required']],
                phone: [null, ['required']],
                role: [null, ['required']],
                role_id: [null, ['required']],
                status: ['ACTIVE', ['required']],
                password: [null, ['required']],
                password_confirmation: [null, ['required']],
                profile: [null, []],
                tmp_file: [null, []],
            }),
            baseUrl: "{{ asset('storage/user') }}/",
            disabled: true,
            dialogData: null,
            validate: null,
            loading: false,
            roleId: @json(auth()?->user()?->role_id),
            init() {
                this.dialogData = this.$store.profileStoreDialog.data;
                if (this.dialogData?.id) {
                    console.log(this.dialogData);
                    this.form.patchValue(this.dialogData ?? {});
                    feather.replace();
                    this.profile_url = this?.dialogData?.profile ? this.dialogData?.profile :
                        null;
                    this.form.tmp_file=this?.dialogData?.profile;
                }
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
                                     this.$store.profileStoreDialog.close(true);
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
@endcomponent
