<template x-dialog="storeReportDocumentCategoryDialog">
    <div x-data="storeReportDocumentCategoryDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 x-show="!dialogData?.id">
                    @lang('form.header.create', ['name' => __('form.title.report_document_category')])
                </h3>
                <h3 x-show="dialogData?.id">
                    @lang('form.header.update', ['name' => __('form.title.report_document_category')])
                </h3>
                <span @click="close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto">
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.title_en')<span>*</span></label>
                        <input placeholder="@lang('form.body.placeholder.title_en')" type="text" x-model="form.title_en"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.title_en" x-text="validate?.title_en"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.title_km')</label>
                        <input placeholder="@lang('form.body.placeholder.title_km')" type="text" x-model="form.title_km"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.title_km" x-text="validate?.title_km"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.ordering')<span>*</span></label>
                        <input id="report_document_category_sequence" placeholder="@lang('form.body.placeholder.ordering')" type="number"
                            x-model="form.sequence" :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.sequence" x-text="validate?.sequence"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.status')<span>*</span> </label>
                        <select x-model="form.status" :disabled="form.disabled">
                            @foreach (config('dummy.status') as $status)
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
        Alpine.data('storeReportDocumentCategoryDialog', () => ({
            form: new FormGroup({
                title_en: [null, ['required']],
                title_km: [null, []],
                sequence: [null, ['required']],
                status: ['ACTIVE', ['required']],
            }),
            dialogData: null,
            validate: null,
            loading: false,
            async init() {
                this.dialogData = this.$dialog('storeReportDocumentCategoryDialog').data;
                if (this.dialogData?.id) {
                    this.form.patchValue(this.dialogData ?? {});
                    this.form.title_en = this.dialogData?.title?.en ?? null;
                    this.form.title_km = this.dialogData?.title?.km ?? null;
                    this.form.sequence = this.dialogData?.sequence ?? null;
                    this.form.status = this.dialogData?.status ?? 'ACTIVE';
                } else {
                    await this.getMaxOrdering((res) => {
                        this.form.sequence = res.max_ordering;
                    });
                }
                feather.replace();
            },
            async getMaxOrdering(callback) {
                await Axios({
                    url: `{{ route('admin-page-report-document-category-max-ordering') }}`,
                    method: 'GET'
                }).then((res) => {
                    callback(res.data)
                }).catch((e) => {
                    console.log(e);
                });
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
                                url: `{{ route('admin-page-report-document-category-save') }}`,
                                method: 'POST',
                                data: {
                                    ...data,
                                    id: this.dialogData?.id,
                                }
                            }).then((res) => {
                                if (res.data.error == false) {
                                    this.form.reset();
                                    this.$dialog('storeReportDocumentCategoryDialog').close(true);
                                }
                                Toast({
                                    message: res.data.message,
                                    status: res.data.status,
                                    size: 'small',
                                });
                            }).catch((e) => {
                                this.validate = e.response?.data?.errors;
                            }).finally(() => {
                                this.form.enable();
                                this.loading = false;
                            });
                        }
                    }
                });
            },
            close() {
                this.$dialog('storeReportDocumentCategoryDialog').close();
            }
        }));
    </script>
</template>
