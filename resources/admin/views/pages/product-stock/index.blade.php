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
            groupedStocks() {
                const rows = Array.isArray(this.table?.data) ? this.table.data : [];
                const grouped = new Map();

                rows.forEach((row) => {
                    const key = `${row.product_id ?? 'null'}`;
                    if (!grouped.has(key)) {
                        grouped.set(key, {
                            product_id: row.product_id,
                            product_name: row.product?.name_en ?? '-',
                            product_sku: row.product?.sku ?? '-',
                            items: [],
                            start_index: 0,
                        });
                    }
                    grouped.get(key).items.push(row);
                });

                let cursor = 0;
                return Array.from(grouped.values()).map((group) => {
                    group.start_index = cursor;
                    cursor += group.items.length;
                    return group;
                });
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
