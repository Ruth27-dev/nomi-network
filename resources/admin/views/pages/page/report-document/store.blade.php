<template x-dialog="storeReportDocumentDialog">
    <div x-data="storeReportDocumentDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 x-show="!dialogData?.id">
                    @lang('form.header.create', ['name' => __('form.title.report_document')])
                </h3>
                <h3 x-show="dialogData?.id">
                    @lang('form.header.update', ['name' => __('form.title.report_document')])
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
                        <label>@lang('form.body.label.category')<span>*</span></label>
                        <select x-model="form.category_id" :disabled="form.disabled">
                            <option value="">@lang('form.body.placeholder.category')</option>
                            <template x-for="item in categories" :key="item.id">
                                <option :value="item.id" x-text="trans(item.title)"></option>
                            </template>
                        </select>
                        <span class="error" x-show="validate?.category_id" x-text="validate?.category_id"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.date')<span>*</span></label>
                        <input id="report_document_date" placeholder="@lang('form.body.placeholder.date')" type="text"
                            x-model="form.date" :disabled="form.disabled" autocomplete="off" readonly>
                        <span class="error" x-show="validate?.date" x-text="validate?.date"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Document File<span>*</span></label>
                        <input type="file" :disabled="form.disabled" id="report_document_file" class="!p-[12px]"
                            @change="onChangeFile($el)">
                        <input type="hidden" x-model="form.tmp_file">
                        <span class="error" x-show="validate?.file" x-text="validate?.file"></span>
                        <template x-if="fileName">
                            <span class="text-xs text-gray-500 mt-1" x-text="fileName"></span>
                        </template>
                        <template x-if="fileUrl">
                            <a :href="fileUrl" target="_blank" class="text-blue-600 underline text-xs mt-1">Current File</a>
                        </template>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.ordering')<span>*</span></label>
                        <input id="report_document_sequence" placeholder="@lang('form.body.placeholder.ordering')" type="number"
                            x-model="form.sequence" :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.sequence" x-text="validate?.sequence"></span>
                    </div>
                </div>
                <div class="row-2">
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
        Alpine.data('storeReportDocumentDialog', () => ({
            form: new FormGroup({
                title_en: [null, ['required']],
                title_km: [null, []],
                category_id: [null, ['required']],
                date: [null, ['required']],
                file: [null, []],
                tmp_file: [null, []],
                sequence: [null, ['required']],
                status: ['ACTIVE', ['required']],
            }),
            categories: [],
            dialogData: null,
            validate: null,
            loading: false,
            fileName: null,
            fileUrl: null,
            trans(value) {
                if (!value) return '-';
                if (typeof value === 'object') {
                    return value?.[langLocale] ?? value?.en ?? '-';
                }
                return value;
            },
            async init() {
                this.dialogData = this.$dialog('storeReportDocumentDialog').data;
                this.categories = this.dialogData?.categories || [];
                if (this.dialogData?.id) {
                    this.form.patchValue(this.dialogData ?? {});
                    this.form.title_en = this.dialogData?.title?.en ?? null;
                    this.form.title_km = this.dialogData?.title?.km ?? null;
                    this.form.category_id = this.dialogData?.add_on?.category_id ?? null;
                    this.form.date = this.formatInputDate(this.dialogData?.add_on?.date);
                    this.form.tmp_file = this.dialogData?.add_on?.file ?? null;
                    this.form.sequence = this.dialogData?.sequence ?? null;
                    this.form.status = this.dialogData?.status ?? 'ACTIVE';
                    this.fileName = this.dialogData?.add_on?.file ?? null;
                    this.fileUrl = this.dialogData?.file_url ?? null;
                } else {
                    await this.getMaxOrdering((res) => {
                        this.form.sequence = res.max_ordering;
                    });
                }
                this.initDatePicker();
                feather.replace();
            },
            async getMaxOrdering(callback) {
                await Axios({
                    url: `{{ route('admin-page-report-document-max-ordering') }}`,
                    method: 'GET'
                }).then((res) => {
                    callback(res.data)
                }).catch((e) => {
                    console.log(e);
                });
            },
            parseDate(date) {
                if (!date) return null;
                const parsedDate = moment(date, [dateRangePickerInputFormat(), 'YYYY-MM-DD', moment.ISO_8601], true);
                return parsedDate.isValid() ? parsedDate : null;
            },
            formatInputDate(date) {
                const parsedDate = this.parseDate(date);
                return parsedDate ? parsedDate.format(dateRangePickerInputFormat()) : (date ?? null);
            },
            initDatePicker() {
                this.$nextTick(() => {
                    const dateRangeFormat = dateRangePickerInputFormat();
                    const input = $('#report_document_date');
                    const datePicker = input.data('daterangepicker');

                    if (datePicker) {
                        datePicker.remove();
                    }
                    input.off('apply.daterangepicker cancel.daterangepicker');
                    input.daterangepicker({
                        showDropdowns: true,
                        singleDatePicker: true,
                        autoUpdateInput: false,
                        minYear: parseInt(moment().format('YYYY'), 10) - 1,
                        maxYear: parseInt(moment().format('YYYY'), 10) + 10,
                        autoApply: true,
                        opens: "center",
                        locale: {
                            format: dateRangeFormat,
                            cancelLabel: 'Clear',
                        }
                    });

                    input.on('apply.daterangepicker', (ev, picker) => {
                        const formattedDate = picker.startDate.format(dateRangeFormat);
                        input.val(formattedDate);
                        this.form.date = formattedDate;
                    });

                    input.on('cancel.daterangepicker', () => {
                        input.val('');
                        this.form.date = null;
                    });

                    const initialDate = this.parseDate(this.form.date);
                    if (initialDate) {
                        input.data('daterangepicker').setStartDate(initialDate);
                        input.data('daterangepicker').setEndDate(initialDate);
                        input.val(initialDate.format(dateRangeFormat));
                        this.form.date = initialDate.format(dateRangeFormat);
                    } else {
                        input.val('');
                    }
                });
            },
            onChangeFile(el) {
                const file = el.files[0] ?? null;
                this.form.file = file;
                if (file) {
                    this.form.tmp_file = null;
                    this.fileName = file.name;
                    this.fileUrl = null;
                }
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
                            let file = document.querySelector('#report_document_file');
                            this.form.file = file.files[0] ?? '';
                            const data = this.form.value();
                            Axios({
                                url: `{{ route('admin-page-report-document-save') }}`,
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
                                    this.$dialog('storeReportDocumentDialog').close(true);
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
                const input = $('#report_document_date');
                const datePicker = input.data('daterangepicker');
                if (datePicker) {
                    datePicker.remove();
                }
                input.off('apply.daterangepicker cancel.daterangepicker');
                this.$dialog('storeReportDocumentDialog').close();
            }
        }));
    </script>
</template>
