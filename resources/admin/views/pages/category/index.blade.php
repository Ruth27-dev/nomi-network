@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="categoryPage">
        @include('admin::shared.header', [
            'title' => __('form.name.category'),
            'header_name' => __('form.name.category'),
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
                    @can('category-create')
                        <button class="btn-create" @click="openStoreCategoryDialog()">
                            <i data-feather="plus"></i>
                            <span class="uppercase">@lang('form.header.button.create')</span>
                        </button>
                    @endcan
                    <button @click="viewTrash()" :class="formFilter.trash ? '!bg-rose-500 !text-white' : '!bg-white !text-rose-500'">
                        <i class="material-icons">delete</i>
                        <span>@lang('form.header.button.trash')</span>
                    </button>
                    <button @click="onReset()">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
            @include('admin::pages.category.table')
        </div>
        @include('admin::pages.category.store')
    </div>
@stop
@section('script')
    <script type="module">
        Alpine.data('categoryPage', () => ({
            table: new Table("{{ route('admin-category-data') }}"),
            init() {
                this.table.init();
                feather.replace();
            },
            formFilter: new FormGroup({
                search: [`{{ request('search') }}`, []],
                trash: [`{{ request('trash') }}`, []],
                shop_id: [`{{ request('shop_id') }}`, []],
                branch_id: [`{{ request('branch_id') }}`, []],
            }),
            onFilter() {
                this.table.init(this.formFilter.value());
            },
            viewTrash() {
                this.formFilter.trash = true;
                this.table.init(this.formFilter.value());
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
            openStoreCategoryDialog(id) {
                this.$dialog('storeCategoryDialog').open({
                    data: {
                        id: id,
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
                                url: `{{ route('admin-category-status') }}`,
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
                                }
                                else{
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
                                url: `{{ route('admin-category-delete') }}`,
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
                                }
                                else{
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
                                url: `{{ route('admin-category-restore') }}`,
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
                                }
                                else{
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
