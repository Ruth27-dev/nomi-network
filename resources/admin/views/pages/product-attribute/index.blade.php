@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="productAttributePage">
        @include('admin::shared.header', [
            'title' => 'Product Attributes',
            'header_name' => 'Product Attributes',
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
                            <input type="text" x-model="formFilter.search" placeholder="Search..." autocomplete="off" @keydown.enter="onFilter()">
                            <button @click="onFilter()"><i data-feather="search"></i></button>
                        </div>
                    </div>
                    @can('product-attribute-create')
                        <button class="btn-create" @click="openStoreDialog()">
                            <i data-feather="plus"></i>
                            <span class="uppercase">Create</span>
                        </button>
                    @endcan
                    <button @click="onReset()">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
            @include('admin::pages.product-attribute.table')
        </div>
        @include('admin::pages.product-attribute.store')
    </div>
@stop
@section('script')
    <script type="module">
        Alpine.data('productAttributePage', () => ({
            table: new Table("{{ route('admin-product-attribute-data') }}"),
            formFilter: new FormGroup({ search: ['', []] }),
            init() {
                this.table.init();
                feather.replace();
            },
            onFilter() {
                this.table.init(this.formFilter.value());
            },
            onReset() {
                this.formFilter.reset();
                this.table.reset();
            },
            openStoreDialog(id = null) {
                this.$dialog('storeProductAttributeDialog').open({
                    data: { id },
                    config: { width: '700px', position: 'right', backdrop: false, blur: 3 },
                    afterClose: (res) => {
                        if (res) this.table.reload();
                    }
                });
            },
            onUpdateStatus(id, status) {
                Axios.post("{{ route('admin-product-attribute-status') }}", { id, status }).then(() => this.table.reload());
            },
            onDelete(id) {
                Axios.delete("{{ route('admin-product-attribute-delete') }}", { data: { id } }).then(() => this.table.reload());
            }
        }));
    </script>
@stop
