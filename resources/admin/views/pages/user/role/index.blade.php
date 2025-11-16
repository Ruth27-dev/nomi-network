@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="rolePage">
        @include('admin::shared.header', [
            'title' => __('form.name.role'),
            'header_name' => __('form.name.role'),
        ])
        <div class="content-body">
            <div class="content-tab">
                <div class="content-tab-wrapper">
                    <span class="title !text-gray-600">
                          @lang('form.total') <span x-text="table?.paginate?.totalItems"></span>
                    </span>
                </div>
                <div class="content-action-button">
                    <div class="filter">
                        <div class="form-row search-inline">
                            <input type="text" x-model="formFilter.search" name="search" placeholder="@lang('form.search_filter.search')"
                                value="{!! request('search') !!}" autocomplete="off" @keydown.enter="onFilter()">
                            <button @click="onFilter()"><i data-feather="search"></i></button>
                        </div>
                    </div>
                    @can('role-create')
                        <button class="btn-create" @click="openStoreRoleDialog()">
                            <i data-feather="plus"></i>
                            <span class="uppercase">@lang('form.header.button.create')</span>
                        </button>
                    @endcan
                    <button @click="onReset()">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
            @include('admin::pages.user.role.table')
        </div>
        @include('admin::pages.user.role.store')
        @include('admin::pages.user.role.permission')
    </div>
@stop
@section('script')
    <script type="module">
        Alpine.data('rolePage', () => ({
            table: new Table("{{ route('admin-user-role-data') }}"),
            init() {
                this.table.init();
                this.initDatePicker();
                 feather.replace();
            },
            formFilter: new FormGroup({
                search: [null || `{{ request('search') }}`, []],
            }),
            onFilter() {
                this.table.init(this.formFilter.value());
            },
            viewTrash() {
                this.table.init({
                    trash: true
                });
            },
            onReset() {
                this.formFilter.reset();
                this.table.reset();
                this.initDatePicker();
            },
            initDatePicker() {
                $('#fromDate, #toDate').daterangepicker({
                    singleDatePicker: true,
                    autoUpdateInput: false,
                    showDropdowns: true,
                    maxYear: parseInt(moment().format('YYYY'), 10) + 2,
                    autoApply: true,
                    opens: "center",
                    locale: {
                        format: 'YYYY-MM-DD',
                        cancelLabel: 'Clear',
                    }
                });
                $('#fromDate').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD'));
                    $('#toDate').data('daterangepicker').minDate = picker.startDate;
                });
                $('#toDate').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD'));
                    $('#fromDate').data('daterangepicker').maxDate = picker.startDate;
                });
            },
            openStoreRoleDialog(data) {
                this.$dialog('storeRoleDialog').open({
                    data: {
                        ...data,
                        tmp_file: data?.profile,
                    },
                    config: {
                        width: '800px',
                        position: 'right',
                        backdrop: false,
                        blur: 3,
                    },
                    afterClose: (res) => {
                        if (res) {
                            this.table.reload();
                        }
                    }
                });

            },
            onUpdateStatus(data, status) {
                this.$store.confirmDialog.open({
                    data: {
                        title: "@lang('dialog.title')",
                        message: (status == active ? `@lang('dialog.msg.enable')` : `@lang('dialog.msg.disable')`) + '?',
                        btnClose: "@lang('dialog.button.close')",
                        btnSave: status == active ? "@lang('dialog.button.enable')" : "@lang('dialog.button.disable')",
                    },
                    afterClosed: (result) => {
                        if (result) {
                            Axios({
                                url: `{{ route('admin-user-role-status') }}`,
                                method: 'POST',
                                data: {
                                    id: data.id,
                                    status: status
                                }
                            }).then((res) => {
                                if (res.data.error == false) {
                                    this.table.reload();
                                }
                                Toast({
                                    message: res.data.message,
                                    status: res.data.status,
                                    size: 'small',
                                });
                            }).catch((e) => {
                                console.log(e);
                            });
                        }
                    }
                });
            },
            openPermissionDialog(data) {
                this.$dialog('userPermission').open({
                    addClass: ['show'],
                    data: data,
                    config: {
                        width: '900px',
                        position: 'right',
                        backdrop: false,
                        blur: 3,
                    },
                    afterClose: (res) => {
                        if (res) {
                            this.table.reload();
                        }
                    }
                });
            }
        }));
    </script>
@stop
