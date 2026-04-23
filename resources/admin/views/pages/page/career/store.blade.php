<template x-dialog="storeCareerDialog">
    <div x-data="storeCareerDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 x-show="!dialogData?.id">
                    @lang('form.header.create', ['name' => __('form.title.career')])
                </h3>
                <h3 x-show="dialogData?.id">
                    @lang('form.header.update', ['name' => __('form.title.career')])
                </h3>
                <span @click="close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto">
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.position_en')<span>*</span></label>
                        <input placeholder="@lang('form.body.placeholder.position')" type="text" x-model="form.position_en"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.position_en" x-text="validate?.position_en"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.position_km')</label>
                        <input placeholder="@lang('form.body.placeholder.position')" type="text" x-model="form.position_km"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.position_km" x-text="validate?.position_km"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.location_en')<span>*</span></label>
                        <input placeholder="@lang('form.body.placeholder.location_en')" type="text" x-model="form.location_en"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.location_en" x-text="validate?.location_en"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.location_km')</label>
                        <input placeholder="@lang('form.body.placeholder.location_km')" type="text" x-model="form.location_km"
                            :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.location_km" x-text="validate?.location_km"></span>
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>@lang('form.body.label.close_date')<span>*</span></label>
                        <input id="career_close_date" placeholder="@lang('form.body.placeholder.date')" type="text"
                            x-model="form.close_date" :disabled="form.disabled" autocomplete="off" readonly>
                        <span class="error" x-show="validate?.close_date" x-text="validate?.close_date"></span>
                    </div>
                    <div class="form-row">
                        <label>@lang('form.body.label.ordering')<span>*</span></label>
                        <input id="career_sequence" placeholder="@lang('form.body.placeholder.ordering')" type="number"
                            x-model="form.sequence" :disabled="form.disabled" autocomplete="off">
                        <span class="error" x-show="validate?.sequence" x-text="validate?.sequence"></span>
                    </div>
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
        Alpine.data('storeCareerDialog', () => ({
            form: new FormGroup({
                position_en: [null, ['required']],
                position_km: [null, []],
                location_en: [null, ['required']],
                location_km: [null, []],
                close_date: [null, ['required']],
                sequence: [null, ['required']],
                status: ['ACTIVE', ['required']],
            }),
            dialogData: null,
            validate: null,
            loading: false,
            async init() {
                this.dialogData = this.$dialog('storeCareerDialog').data;
                if (this.dialogData?.id) {
                    this.form.patchValue(this.dialogData ?? {});
                    this.form.position_en = this.dialogData?.title?.en ?? null;
                    this.form.position_km = this.dialogData?.title?.km ?? null;
                    this.form.location_en = this.dialogData?.description?.en ?? null;
                    this.form.location_km = this.dialogData?.description?.km ?? null;
                    this.form.close_date = this.formatInputDate(this.dialogData?.add_on?.close_date);
                    this.form.sequence = this.dialogData?.sequence ?? null;
                    this.form.status = this.dialogData?.status ?? 'ACTIVE';
                } else {
                    await this.getMaxOrdering((res) => {
                        this.form.sequence = res.max_ordering;
                    });
                }
                this.initCloseDatePicker();
                feather.replace();
            },
            async getMaxOrdering(callback) {
                await Axios({
                    url: `{{ route('admin-page-career-max-ordering') }}`,
                    method: 'GET',
                    params: {}
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
            initCloseDatePicker() {
                this.$nextTick(() => {
                    const dateRangeFormat = dateRangePickerInputFormat();
                    const input = $('#career_close_date');
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
                        this.form.close_date = formattedDate;
                    });

                    input.on('cancel.daterangepicker', () => {
                        input.val('');
                        this.form.close_date = null;
                    });

                    const initialDate = this.parseDate(this.form.close_date);
                    if (initialDate) {
                        input.data('daterangepicker').setStartDate(initialDate);
                        input.data('daterangepicker').setEndDate(initialDate);
                        input.val(initialDate.format(dateRangeFormat));
                        this.form.close_date = initialDate.format(dateRangeFormat);
                    } else {
                        input.val('');
                    }
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
                                url: `{{ route('admin-page-career-save') }}`,
                                method: 'POST',
                                data: {
                                    ...data,
                                    id: this.dialogData?.id,
                                }
                            }).then((res) => {
                                if (res.data.error == false) {
                                    this.form.reset();
                                    this.$dialog('storeCareerDialog').close(true);
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
                const input = $('#career_close_date');
                const datePicker = input.data('daterangepicker');
                if (datePicker) {
                    datePicker.remove();
                }
                input.off('apply.daterangepicker cancel.daterangepicker');
                this.$dialog('storeCareerDialog').close();
            }
        }));
    </script>
</template>
