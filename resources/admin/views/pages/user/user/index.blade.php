@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="userPage">
        @include('admin::shared.header', [
            'title' => __('form.name.user_admin'),
            'header_name' => __('form.name.user_admin'),
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
                    @can('user-create')
                        <button class="btn-create" @click="openStoreUserDialog()">
                            <i data-feather="plus"></i>
                            <span class="uppercase">@lang('form.header.button.create')</span>
                        </button>
                    @endcan
                    <button @click="viewTrash()" class="!text-rose-500">
                        <i class="material-icons">delete</i>
                        <span>@lang('form.header.button.trash')</span>
                    </button>
                    <button @click="onReset()">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
            @include('admin::pages.user.user.table')
        </div>
        @include('admin::pages.user.user.store')
        @include('admin::pages.user.user.change-password')
    </div>
@stop
@section('script')
    <script type="module">
        Alpine.data('userPage', () => ({
            table: new Table("{{ route('admin-user-data') }}"),
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
            openStoreUserDialog(data) {
                this.$dialog('storeUserDialog').open({
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
            openChangePasswordDialog(data) {
                this.$dialog('changePasswordDialog').open({
                    data: data,
                    config: {
                        width: '800px',
                        position: 'center',
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
                                url: `{{ route('admin-user-status') }}`,
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
            onDelete(data) {
                this.$store.confirmDialog.open({
                    data: {
                        title: "@lang('dialog.title')",
                        message: `@lang('dialog.msg.move_to_trash')`,
                        btnClose: "@lang('dialog.button.close')",
                        btnSave: "@lang('dialog.button.move_to_trash')",
                    },
                    afterClosed: (result) => {
                        if (result) {
                            Axios({
                                url: `{{ route('admin-user-delete') }}`,
                                method: 'DELETE',
                                data: {
                                    id: data.id
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
            onRestore(data) {
                this.$store.confirmDialog.open({
                    data: {
                       title: "@lang('dialog.title')",
                        message: `@lang('dialog.msg.restore')`,
                        btnClose: "@lang('dialog.button.close')",
                        btnSave: "@lang('dialog.button.restore')",
                    },
                    afterClosed: (result) => {
                        if (result) {
                            Axios({
                                url: `{{ route('admin-user-restore') }}`,
                                method: 'PUT',
                                data: {
                                    id: data.id
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
