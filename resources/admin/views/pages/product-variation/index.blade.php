@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="productVariationPage">
        @include('admin::shared.header', [
            'title' => __('form.name.product_variation'),
            'header_name' => __('form.name.product_variation'),
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
                            <input type="text" x-model="formFilter.search" name="search" placeholder="..."
                                value="{!! request('search') !!}" autocomplete="off" @keydown.enter="onFilter()">
                            <button @click="onFilter()"><i data-feather="search"></i></button>
                        </div>
                        <div class="form-row !min-w-[220px]">
                            <select x-model="formFilter.product_id" @change="onFilter()">
                                <option value="">All Products</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->title[app()->getLocale()] ?? $product->title['en'] ?? 'Untitled' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @can('product-create')
                        <button class="btn-create" @click="openStoreVariationDialog()">
                            <i data-feather="plus"></i>
                            <span class="uppercase">@lang('form.header.button.create')</span>
                        </button>
                    @endcan
                    <button @click="onReset()">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
            @include('admin::pages.product-variation.table')
        </div>
        @include('admin::pages.product-variation.store')
    </div>
@stop
@section('script')
    <script type="module">
        Alpine.data('productVariationPage', () => ({
            table: new Table("{{ route('admin-product-variation-data') }}"),
            init() {
                this.table.init();
                feather.replace();
            },
            formFilter: new FormGroup({
                search: [`{{ request('search') }}`, []],
                product_id: [`{{ request('product_id') }}`, []],
            }),
            onFilter() {
                this.table.init(this.formFilter.value());
            },
            onReset() {
                this.formFilter.reset();
                this.table.reset();
            },
            openStoreVariationDialog(id) {
                this.$dialog('storeVariationDialog').open({
                    data: {
                        id: id,
                    },
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
                                url: `{{ route('admin-product-variation-status') }}`,
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
                                url: `{{ route('admin-product-variation-delete') }}`,
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
        }));
    </script>
@stop
