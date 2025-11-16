@component('admin::components.dialog', ['dialog' => 'confirmDialog'])
    <div x-data="confirmDialog" class="dialog-form" style="width: 300px;">
        <div class="dialog-form-header font-bold">
            <h3 x-text="data?.title"></h3>
        </div>
        <div class="dialog-form-body">
            <div class="form-row">
                <p x-html="data?.message"></p>
            </div>
            <template x-if="data?.allow_checkbox">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault"
                        x-model="checkbox" />
                    <label class="form-check-label" for="flexSwitchCheckDefault">@lang('file-manager.delete-file.form.to_trash.label')</label>
                </div>
            </template>
        </div>
        <div class="dialog-form-footer">
            <button type="button" class="close" @click="$store.confirmDialog.close(false)"
                x-text="data?.btnClose || 'Close'"></button>
            <button type="button" @click="onConfirm" x-bind:disabled="disabled || loading">
                <span x-text="data?.btnSave || 'Save'"></span>
                <div class="loader" style="display: none" x-show="loading"></div>
            </button>
        </div>
    </div>
    <script>
        Alpine.data("confirmDialog", () => ({
            data: null,
            disabled: false,
            loading: false,
            checkbox: true,
            init() {
                this.data = this.$store.confirmDialog.data;
                feather.replace();
            },
            onConfirm() {
                let response = {
                    checkbox: this.data?.allow_checkbox ? this.checkbox : undefined
                };
                if (!this.$store.confirmDialog.beforeClose || typeof this.$store.confirmDialog.beforeClose ===
                    'undefined') {
                    this.$store.confirmDialog.close(response);
                    this.disabled = true;
                    return;
                }
                this.$store.confirmDialog.beforeClose(response, (close, loading) => {
                    this.disabled = true;
                    this.loading = loading;
                    if (close) {
                        this.$store.confirmDialog.close(response);
                    }
                });
            }
        }))
    </script>
@endcomponent
