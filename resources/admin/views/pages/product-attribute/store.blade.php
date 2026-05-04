<template x-dialog="storeProductAttributeDialog">
    <div x-data="storeProductAttributeDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 x-show="!dialogData?.id" class="text-gray-600">Create Product Attribute</h3>
                <h3 x-show="dialogData?.id" class="text-gray-600">Update Product Attribute</h3>
                <span @click="$dialog('storeProductAttributeDialog').close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto">
                <div class="row-2">
                    <div class="form-row">
                        <label>Name<span>*</span></label>
                        <input type="text" placeholder="Attribute name" x-model="form.name" :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.name" x-text="validate?.name"></span>
                    </div>
                    <div class="form-row">
                        <label>SKU<span>*</span></label>
                        <input type="text" placeholder="attribute-sku" x-model="form.sku" :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.sku" x-text="validate?.sku"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Input Type<span>*</span></label>
                        <select x-model="form.input_type" :disabled="form.disabled">
                            <option value="select">Select</option>
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="color">Color</option>
                            <option value="size">Size</option>
                            <option value="button">Button</option>
                        </select>
                        <span class="error" x-show="validate?.input_type" x-text="validate?.input_type"></span>
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
                    <label><input type="checkbox" x-model="form.is_variation" :disabled="form.disabled"> Use for variation</label>
                </div>

                <div class="form-row">
                    <label>Attribute Values</label>
                    <div class="w-full rounded border border-[#d1d5db] bg-white px-2.5 py-2 flex flex-wrap gap-1.5 focus-within:border-(--primary,#6366f1) focus-within:ring-1 focus-within:ring-(--primary,#6366f1) transition min-h-10"
                        :class="form.disabled ? 'bg-gray-50 opacity-60 pointer-events-none' : ''">
                        <template x-for="(value, index) in form.values" :key="index">
                            <span class="inline-flex items-center gap-1 rounded bg-primary/10 border border-primary/20 pl-2.5 pr-1 py-0.5 text-xs font-medium text-primary">
                                <span x-text="value"></span>
                                <button type="button" @click="removeValue(index)"
                                    class="flex items-center justify-center w-4 h-4 rounded hover:bg-primary/20 transition text-primary/60 hover:text-primary">
                                    <span class="material-icons" style="font-size:12px;line-height:1">close</span>
                                </button>
                            </span>
                        </template>
                        <input type="text" x-model="newValue"
                            placeholder="Type and press Enter to add…"
                            @keydown.enter.prevent="addValue()"
                            @keydown.188.prevent="addValue()"
                            :disabled="form.disabled"
                            autocomplete="off"
                            class="flex-1 min-w-[140px] border-none outline-none bg-transparent text-sm text-gray-700 placeholder-gray-400 py-0.5 pl-[5px]">
                    </div>
                    <span class="error" x-show="validate?.values" x-text="validate?.values"></span>
                </div>
            </div>
            <div class="form-footer">
                <div class="form-button">
                    <button type="button" color="primary" @click="onSave()" :disabled="form.disabled || loading">
                        <span class="material-icons mr-1">save</span>
                        <span>Save</span>
                        <div class="loader" style="display: none" x-show="loading"></div>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        Alpine.data('storeProductAttributeDialog', () => ({
            form: new FormGroup({
                name: ['', []],
                sku: ['', []],
                input_type: ['select', []],
                status: ['ACTIVE', []],
                is_variation: [true, []],
                values: [[], []],
            }),
            newValue: '',
            dialogData: null,
            validate: null,
            loading: false,
            async init() {
                this.dialogData = this.$dialog('storeProductAttributeDialog').data;
                if (this.dialogData?.id) {
                    const res = await Axios.get("{{ route('admin-product-attribute-detail') }}", { params: { id: this.dialogData.id } });
                    const d = res.data.data;
                    this.form.name = d.name;
                    this.form.sku = d.code;
                    this.form.input_type = d.input_type;
                    this.form.status = d.status;
                    this.form.is_variation = !!d.is_variation;
                    this.form.values = (d.values ?? []).map(v => v.value).filter(v => v);
                }
                feather.replace();
            },
            addValue() {
                const v = this.newValue.trim();
                if (v && !this.form.values.includes(v)) {
                    this.form.values.push(v);
                }
                this.newValue = '';
            },
            removeValue(index) {
                this.form.values.splice(index, 1);
            },
            async onValidate(callback) {
                this.validate = null;
                this.loading = true;
                this.form.disable();
                await Axios({
                    url: `{{ route('admin-validation-product-attribute') }}`,
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
                    this.validate = res?.response?.data?.errors;
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
                                    url: `{{ route('admin-product-attribute-save') }}`,
                                    method: 'POST',
                                    data: { ...this.form.value(), id: this.dialogData?.id },
                                }).then((res) => {
                                    if (res.data.error == false) {
                                        this.form.reset();
                                        this.$dialog('storeProductAttributeDialog').close(true);
                                    }
                                    toastr.success(res.data.message, { progressBar: true, timeOut: 5000 });
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
            },
        }));
    </script>
</template>
