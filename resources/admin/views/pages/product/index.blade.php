@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="productPage">
        @include('admin::shared.header', [
            'title' => __('form.name.product'),
            'header_name' => __('form.name.product'),
        ])
        <div class="content-body">
            <div class="content-tab">
                <div class="content-tab-wrapper">
                    <span class="title !text-gray-600">
                        All Items : <span x-text="table?.paginate?.totalItems"></span>
                    </span>
                </div>
                <div class="content-action-button">
                    <div class="filter">
                        <div class="form-row search-inline">
                            <input type="text" x-model="formFilter.search" name="search" placeholder="..."
                                value="{!! request('search') !!}" autocomplete="off" @keydown.enter="onFilter()">
                            <button @click="onFilter()"><i data-feather="search"></i></button>
                        </div>
                        <div class="filter">
                            <button class="btn-search" style="background-color: #0069B9;" @click="searchDialog()">
                                <i data-feather="filter"></i>
                                <span>@lang('form.button.filter')</span>
                            </button>
                        </div>
                    </div>
                    @can('product-create')
                        <button class="btn-create" @click="openStoreItemDialog()">
                            <i data-feather="plus"></i>
                            <span class="uppercase">@lang('form.header.button.create')</span>
                        </button>
                    @endcan
                    <button @click="viewTrash()"
                        :class="formFilter.trash ? '!bg-rose-500 !text-white' : '!bg-white !text-rose-500'">
                        <i class="material-icons">delete</i>
                        <span>@lang('form.header.button.trash')</span>
                    </button>
                    <button @click="onReset()">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
            @include('admin::pages.product.table')
        </div>
        @include('admin::pages.product.store')
        @include('admin::pages.product.copy')
        @include('admin::pages.product.view-variation')
        @include('admin::pages.product.form-search')
    </div>
@stop
@section('script')
    <script type="module">
        Alpine.data('productPage', () => ({
            table: new Table("{{ route('admin-product-data', $type) }}"),
            showDialogSearch: false,
            init() {
                this.table.init();
                feather.replace();
            },
            formFilter: new FormGroup({
                search: [`{{ request('search') }}`, []],
                trash: [`{{ request('trash') }}`, []],
                category_id: [`{{ request('category_id') }}`, []],
            }),
            onFilter() {
                this.showDialogSearch = false;
                this.table.init(this.formFilter.value());
            },
            viewTrash() {
                this.formFilter.trash = true;
                this.formFilter.search = '';
                this.table.init(this.formFilter.value());
            },
            onReset() {
                this.formFilter.reset();
                this.table.reset();
            },
            searchDialog() {
                this.$nextTick(() => {
                    feather.replace();
                });
                this.showDialogSearch = true;
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
            openStoreItemDialog(id) {
                this.$dialog('storeItemDialog').open({
                    data: {
                        id: id,
                    },
                    config: {
                        width: '1000px',
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
            openStoreCopyItemDialog(id) {
                this.$dialog('storeCopyItemDialog').open({
                    data: {
                        id: id,
                    },
                    config: {
                        width: '1000px',
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
            openViewVariationDialog(id) {
                this.$dialog('viewVariationDialog').open({
                    data: {
                        id: id,
                    },
                    config: {
                        width: '85%',
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
            onUpdateStatus(id, status) {
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
                                url: `{{ route('admin-product-status') }}`,
                                method: 'POST',
                                data: {
                                    id: id,
                                    status: status
                                }
                            }).then((res) => {
                                if (res.data.error == false) {
                                    this.table.reload();
                                    toastr.success(res.data.message, {
                                        progressBar: true,
                                        timeOut: 5000
                                    });
                                } else {
                                    toastr.error(res.data.message, {
                                        progressBar: true,
                                        timeOut: 5000
                                    });
                                }

                            }).catch((e) => {
                                console.log(e);
                            });
                        }
                    }
                });
            },
            onDelete(id) {
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
                                url: `{{ route('admin-product-delete') }}`,
                                method: 'DELETE',
                                data: {
                                    id: id
                                }
                            }).then((res) => {
                                if (res.data.error == false) {
                                    this.table.reload();
                                    toastr.success(res.data.message, {
                                        progressBar: true,
                                        timeOut: 5000
                                    });
                                } else {
                                    toastr.error(res.data.message, {
                                        progressBar: true,
                                        timeOut: 5000
                                    });
                                }
                            }).catch((e) => {
                                console.log(e);
                            });
                        }
                    }
                });
            },
            onRestore(id) {
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
                                url: `{{ route('admin-product-restore') }}`,
                                method: 'PUT',
                                data: {
                                    id: id
                                }
                            }).then((res) => {
                                if (res.data.error == false) {
                                    this.table.reload();
                                    toastr.success(res.data.message, {
                                        progressBar: true,
                                        timeOut: 5000
                                    });
                                } else {
                                    toastr.error(res.data.message, {
                                        progressBar: true,
                                        timeOut: 5000
                                    });
                                }
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
