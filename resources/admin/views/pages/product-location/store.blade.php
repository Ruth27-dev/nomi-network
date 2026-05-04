<template x-dialog="storeProductLocationDialog">
    <div x-data="storeProductLocationDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 x-show="!data" class="text-gray-600">Create Product Location</h3>
                <h3 x-show="data?.id" class="text-gray-600">Update Product Location</h3>
                <span @click="$dialog('storeProductLocationDialog').close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto min-h-0">
                <div class="row-2">
                    <div class="form-row">
                        <label>Name<span>*</span></label>
                        <input type="text" placeholder="Location name" x-model="form.name_en"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.name_en" x-text="validate?.name_en"></span>
                    </div>
                    <div class="form-row">
                        <label>Status<span>*</span></label>
                        <select x-model="form.status" :disabled="form.disabled">
                            <option value="ACTIVE">Active</option>
                            <option value="INACTIVE">Inactive</option>
                        </select>
                        <span class="error" x-show="validate?.status" x-text="validate?.status"></span>
                    </div>
                </div>
                <div class="form-row">
                    <label>Type<span>*</span></label>
                    <select x-model="form.location_type" :disabled="form.disabled">
                        <option value="province">Province</option>
                        <option value="city">City</option>
                        <option value="district">District</option>
                        <option value="other">Other</option>
                    </select>
                    <span class="error" x-show="validate?.location_type" x-text="validate?.location_type"></span>
                </div>
                <div class="form-row">
                    <label>Description</label>
                    <textarea placeholder="Additional details…" x-model="form.address"
                        :disabled="form.disabled" autocomplete="off" rows="3"></textarea>
                    <span class="error" x-show="validate?.address" x-text="validate?.address"></span>
                </div>
            </div>
            <div class="form-footer sticky bottom-0 bg-white z-10">
                <div class="form-button">
                    <button type="button" color="primary" @click="onSave()"
                        :disabled="form.disabled || loading">
                        <span class="material-icons mr-1">save</span>
                        <span>Save</span>
                        <div class="loader" style="display: none" x-show="loading"></div>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        Alpine.data('storeProductLocationDialog', () => ({
            form: new FormGroup({
                name_en:       ['', ['required']],
                location_type: ['province', ['required']],
                address:       ['', []],
                status:        ['ACTIVE', ['required']],
            }),
            data: null,
            dialogData: null,
            validate: null,
            loading: false,
            async init() {
                this.dialogData = this.$dialog('storeProductLocationDialog').data;
                if (this.dialogData?.id) {
                    const res = await Axios.get("{{ route('admin-product-location-detail') }}", {
                        params: { id: this.dialogData.id }
                    });
                    this.data = res.data.data;
                    this.setValue(this.data);
                }
                feather.replace();
            },
            setValue(data) {
                this.form.name_en       = data?.name_en ?? '';
                this.form.location_type = data?.location_type ?? 'province';
                this.form.address       = data?.address ?? '';
                this.form.status        = data?.status ?? 'ACTIVE';
            },
            async onValidate(callback) {
                this.validate = null;
                this.loading = true;
                this.form.disable();
                await Axios({
                    url: "{{ route('admin-validation-product-location') }}",
                    method: 'POST',
                    data: { ...this.form.value(), id: this.dialogData?.id },
                }).then((res) => {
                    callback(res?.data);
                }).catch((error) => {
                    callback(error);
                }).finally(() => {
                    this.loading = false;
                    this.form.enable();
                });
            },
            async onSave() {
                await this.onValidate((res) => {
                    this.validate = res?.response?.data?.errors ?? null;
                    if (res?.status == 422) {
                        toastr.info(res.response.data.message, { progressBar: true, timeOut: 5000 });
                    }
                });
                if (!this.validate) {
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
                                Axios({
                                    url: "{{ route('admin-product-location-save') }}",
                                    method: 'POST',
                                    data: { ...this.form.value(), id: this.dialogData?.id },
                                }).then((res) => {
                                    if (res.data.error == false) {
                                        this.form.reset();
                                        this.$dialog('storeProductLocationDialog').close(true);
                                        toastr.success(res.data.message, { progressBar: true, timeOut: 5000 });
                                    } else {
                                        toastr.error(res.data.message, { progressBar: true, timeOut: 5000 });
                                    }
                                }).catch((e) => {
                                    this.validate = e?.response?.data?.errors ?? null;
                                    toastr.error(e?.response?.data?.message ?? 'Something went wrong!', {
                                        progressBar: true, timeOut: 5000
                                    });
                                }).finally(() => {
                                    this.form.enable();
                                    this.loading = false;
                                });
                            }
                        }
                    });
                }
            },
        }));
    </script>
</template>
