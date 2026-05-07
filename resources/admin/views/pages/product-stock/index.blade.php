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
                    class="px-3 py-1.5 rounded text-sm border transition"
                    :style="activeTab === 'inventory'
                        ? 'background-color:#2563eb;color:#ffffff;border-color:#2563eb;'
                        : 'background-color:#ffffff;color:#4b5563;border-color:#d1d5db;'"
                    @click="switchTab('inventory')">
                    Inventory
                </button>
                <button
                    class="px-3 py-1.5 rounded text-sm border transition"
                    :style="activeTab === 'summary'
                        ? 'background-color:#2563eb;color:#ffffff;border-color:#2563eb;'
                        : 'background-color:#ffffff;color:#4b5563;border-color:#d1d5db;'"
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
                <div class="content-action-button flex-wrap gap-2">
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
                    <a href="{{ route('admin-product-stock-report') }}"
                        class="btn-create flex items-center gap-1.5 h-[35px] px-3 rounded bg-[#30ace2]! text-white! no-underline">
                        <i data-feather="file-text"></i>
                        <span class="uppercase text-xs">Stock Report</span>
                    </a>
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
