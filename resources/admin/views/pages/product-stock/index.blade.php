@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="productStockPage">
        @include('admin::shared.header', [
            'title' => 'Stock Inventory',
            'header_name' => 'Stock Inventory',
        ])
        <div class="content-body">
            <div class="mb-3 flex items-center gap-2">
                <button
                    class="px-3 py-1.5 rounded text-sm border"
                    :class="activeTab === 'inventory' ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-300'"
                    @click="switchTab('inventory')">
                    Inventory
                </button>
                <button
                    class="px-3 py-1.5 rounded text-sm border"
                    :class="activeTab === 'summary' ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-300'"
                    @click="switchTab('summary')">
                    Movement Summary
                </button>
            </div>
            <div class="content-tab">
                <div class="content-tab-wrapper">
                    <span class="title !text-gray-600">
                        @lang('form.total') <span x-text="currentTable()?.paginate?.totalItems"></span>
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
            <template x-if="activeTab === 'inventory'">
                <div>
                    @include('admin::pages.product-stock.table')
                </div>
            </template>
            <template x-if="activeTab === 'summary'">
                <div>
                    @include('admin::pages.product-stock.summary-table')
                </div>
            </template>
        </div>
        @include('admin::pages.product-stock.store')
    </div>
@stop
@section('script')
    <script type="module">
        Alpine.data('productStockPage', () => ({
            inventoryTable: new Table("{{ route('admin-product-stock-data') }}"),
            summaryTable: new Table("{{ route('admin-product-stock-history') }}"),
            activeTab: 'inventory',
            formFilter: new FormGroup({ search: ['', []] }),
            selectedStock: null,
            init() {
                this.inventoryTable.init();
                feather.replace();
            },
            currentTable() {
                return this.activeTab === 'summary' ? this.summaryTable : this.inventoryTable;
            },
            switchTab(tab) {
                this.activeTab = tab;
                this.onReset();
                if (tab === 'summary') {
                    this.summaryTable.init({ summary: true });
                } else {
                    this.inventoryTable.init();
                }
            },
            onFilter() {
                const filter = this.formFilter.value();
                if (this.activeTab === 'summary') {
                    this.summaryTable.init({ ...filter, summary: true });
                } else {
                    this.inventoryTable.init(filter);
                }
            },
            onReset() {
                this.formFilter.reset();
                if (this.activeTab === 'summary') {
                    this.summaryTable.init({ summary: true });
                } else {
                    this.inventoryTable.reset();
                }
            },
            openAdjustDialog(stock) {
                this.selectedStock = stock;
                this.$dialog('adjustStockDialog').open({
                    data: { stock },
                    config: { width: '520px', position: 'right', backdrop: false, blur: 3 },
                    afterClose: (res) => {
                        if (res) this.inventoryTable.reload();
                    }
                });
            },
        }));
    </script>
@stop
