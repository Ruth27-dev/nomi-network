@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="bankAccountPage">
        @include('admin::shared.header', [
            'title' => __('form.name.bank_account'),
            'header_name' => __('form.name.bank_account'),
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
                    @can('bank-account-create')
                        <button class="btn-create" @click="openStoreBankAccountDialog()">
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
            @include('admin::pages.setting.bank-account.table')
        </div>
        @include('admin::pages.setting.bank-account.store')
    </div>
@stop
@section('script')
<script>
    window.PAGE_CONFIG = @json(config('dummy.page'));
</script>
    <script type="module">
        Alpine.data('bankAccountPage', () => ({
            table: new Table("{{ route('admin-setting-bank-account-data') }}"),
            init() {
                this.table.init();
                console.log(this.table);
                
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
            },
            onViewImage(path) {
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
            openStoreBankAccountDialog(data) {
                this.$dialog('storeBankAccountDialog').open({
                    data: {
                        ...data,
                        tmp_file: data?.qr_code,
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
                                url: `{{ route('admin-setting-bank-account-status') }}`,
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
                                url: `{{ route('admin-setting-bank-account-delete') }}`,
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
                                url: `{{ route('admin-setting-bank-account-restore') }}`,
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
        }));
    </script>
@stop
