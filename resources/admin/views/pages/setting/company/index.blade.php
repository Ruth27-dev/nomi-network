@extends('admin::shared.layout')
@section('style')
    <link rel="stylesheet" href="{{ asset('plugin/css/form.css') }}">
@endsection
@section('layout')
    <div class="form-admin" x-data="companyPage">
        @include('admin::shared.header', [
            'title' => __('form.name.company'),
            'header_name' => __('form.name.company'),
        ])
        <form id="form" class="form-wrapper">
            <div class="form-header"></div>
            <div class="form-body">
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.name_en')<span>*</span></label>
                        <input x-model="form.name_en" :disabled="form.disabled" placeholder="@lang('form.body.placeholder.name_en')" />
                        <span class="error" x-show="validate?.name_en" x-text="validate?.name_en"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.name_km')</label>
                        <input x-model="form.name_km" :disabled="form.disabled" placeholder="@lang('form.body.placeholder.name_km')" />
                        <span class="error" x-show="validate?.name_km" x-text="validate?.name_km"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.phone')<span>*</span></label>
                        <input x-model="form.phone_en" :disabled="form.disabled" placeholder="@lang('form.body.placeholder.phone_en')" />
                        <span class="error" x-show="validate?.phone_en" x-text="validate?.phone_en"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.vat_tin')</label>
                        <input x-model="form.vat_tin" :disabled="form.disabled" placeholder="@lang('form.body.placeholder.vat_tin')" />
                        <span class="error" x-show="validate?.vat_tin" x-text="validate?.vat_tin"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.address_en')<span>*</span></label>
                        <textarea x-model="form.address_en" rows="5" :disabled="form.disabled" placeholder="@lang('form.body.placeholder.address_en')"></textarea>
                        <span class="error" x-show="validate?.address_en" x-text="validate?.address_en"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.address_km')</label>
                        <textarea x-model="form.address_km" rows="5" :disabled="form.disabled" placeholder="@lang('form.body.placeholder.address_km')"></textarea>
                        <span class="error" x-show="validate?.address_km" x-text="validate?.address_km"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.logo') <span>*</span></label>
                        <input type="file" :disabled="form.disabled" accept="image/*" id="logo" class="!p-[12px]"
                            @change="onPreviewLogo($el)">
                        <input type="hidden" x-model="form.tmp_file">
                        <template x-if="logo_url">
                            <div
                                class="h-[250px] rounded-md border border-gray-100 overflow-hidden relative grid place-items-center group mt-2">
                                <img class="w-full h-full object-contain" :src="logo_url" alt="">
                                <div class="absolute flex gap-2 opacity-0 group-hover:opacity-100 duration-[0.2s]">
                                    <button type="button"
                                        class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                        @click="onViewLogo(logo_url)">
                                        <span class="material-icons-outlined text-white text-2xl w-[24px]">
                                            visibility_on
                                        </span>
                                    </button>
                                    <button type="button"
                                        class="bg-black/80 w-[50px] h-[50px] border border-white rounded-full grid place-items-center"
                                        @click="onRemoveLogo()">
                                        <span class="material-icons-outlined text-white text-2xl w-[24px]">
                                            delete
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <span class="error" x-show="validate?.logo" x-text="validate?.logo"></span>
                    </div>

                </div>
                <div class="form-button">
                    @can('about-us-update')
                        <button type="button" @click="onSave()" :disabled="form.disabled || loading" color="primary"
                            class="!rounded-[50px]">
                            <span class="material-icons mr-1">save</span>
                            <span>Save</span>
                            <div class="loader" style="display: none" x-show="loading"></div>
                        </button>
                    @endcan
                </div>
            </div>
            <div class="form-footer"></div>
        </form>
    </div>
@stop
@section('script')
    <script type="module">
        Alpine.data('companyPage', () => ({
            form: new FormGroup({
                type: ['company', ['required']],
                name_en: [null, ['required']],
                name_km: [null, ['required']],
                address_en: [null, ['required']],
                address_km: [null, ['required']],
                phone_en: [null, ['required']],
                phone_km: [null, ['required']],
                vat_tin: [null, ['required']],
                logo: [null, []],
                tmp_file: [null, []],
                status: ['ACTIVE', ['required']],
            }),
            id: null,
            validate: null,
            loading: false,
            logo_url: null,
            init() {
                feather.replace();
                let data = @json($company);
                if (data) {
                    this.id = data.id;
                    this.form.name_en = data?.add_on?.name?.en;
                    this.form.name_km = data?.add_on?.name?.km;
                    this.form.address_en = data?.add_on?.address?.en;
                    this.form.address_km = data?.add_on?.address?.km;
                    this.form.phone_en = data?.add_on?.phone?.en;
                    this.form.phone_km = data?.add_on?.phone?.km;
                    this.form.vat_tin = data?.add_on?.vat_tin;
                    this.logo_url = data?.image_url ? data?.image_url : null;
                    this.form.tmp_file = data?.image;
                }
            },
            onPreviewLogo(el) {
                const logo = URL.createObjectURL(el.files[0]);
                this.logo_url = logo;
            },
            onViewLogo(path) {
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
            onRemoveLogo() {
                this.form.tmp_file = null;
                this.logo_url = null;
                document.querySelector('#logo').value = '';
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

                            const file = document.querySelector('#logo');
                            this.form.logo = file.files.length ? file.files[0] : undefined;

                            const data = new FormData();
                            data.append('id', this.id ?? '');

                            for (const [key, value] of Object.entries(this.form.value())) {
                                if (value !== null && value !== undefined) {
                                    data.append(key, value);
                                }
                            }

                            Axios({
                                url: `{{ route('admin-setting-company-save') }}`,
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'multipart/form-data',
                                },
                                data: data,
                            }).then((res) => {
                                this.id = res.data.id;
                                Toast({
                                    message: res.data.message,
                                    status: res.data.status,
                                    size: 'small',
                                });
                                if (res.data.error == false) {
                                    location.reload();
                                }
                            }).catch((e) => {
                                this.validate = e.response.data.errors;
                            }).finally(() => {
                                this.form.enable();
                                this.loading = false;
                            });
                        }
                    }
                });
            }

        }));
    </script>
@endsection
