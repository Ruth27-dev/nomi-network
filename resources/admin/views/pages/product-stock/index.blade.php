@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="productStockPage">
        @include('admin::shared.header', [
            'title' => 'Stock Inventory',
            'header_name' => 'Stock Inventory',
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
                            <input type="text" x-model="formFilter.search" placeholder="Search product or SKU..."
                                autocomplete="off" @keydown.enter="onFilter()">
                            <button @click="onFilter()"><i data-feather="search"></i></button>
                        </div>
                    </div>
                    <button @click="onReset()">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
            @include('admin::pages.product-stock.table')
        </div>
        @include('admin::pages.product-stock.store')
    </div>
@stop
@section('script')
    <script type="module">
        Alpine.data('productStockPage', () => ({
            table: new Table("{{ route('admin-product-stock-data') }}"),
            formFilter: new FormGroup({ search: ['', []] }),
            selectedStock: null,
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
            openAdjustDialog(stock) {
                this.selectedStock = stock;
                this.$dialog('adjustStockDialog').open({
                    data: { stock },
                    config: { width: '520px', position: 'right', backdrop: false, blur: 3 },
                    afterClose: (res) => {
                        if (res) this.table.reload();
                    }
                });
            },
        }));
    </script>
@stop

