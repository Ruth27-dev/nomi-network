@extends('admin::shared.layout')
@section('style')
    <style>
        .stock-stats-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 16px;
        }

        .stock-stat-card {
            align-items: center;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            display: flex;
            gap: 12px;
            min-height: 72px;
            padding: 12px 16px;
        }

        .stock-stat-card.is-danger {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .stock-stat-card.is-warning {
            background: #fffbeb;
            border-color: #fde68a;
        }

        .stock-stat-card.is-success {
            background: #ecfdf5;
            border-color: #a7f3d0;
        }

        .stock-table-scroll {
            overflow-x: auto;
            width: 100%;
        }

        .stock-inventory-grid,
        .stock-summary-grid {
            align-items: center;
            column-gap: 12px;
            display: grid;
            min-width: 980px;
            width: 100%;
        }

        .stock-inventory-grid {
            grid-template-columns: 56px minmax(240px, 1.7fr) 110px 110px 120px 150px 190px 100px;
        }

        .stock-summary-grid {
            grid-template-columns: 56px minmax(240px, 1.5fr) minmax(200px, 1.2fr) 130px 190px 110px;
        }

        .stock-table-head {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 13px;
            font-weight: 700;
            height: 44px;
            padding: 0 12px;
        }

        .stock-table-row {
            border-bottom: 1px solid #e5e7eb;
            min-height: 72px;
            padding: 0 12px;
            transition: background-color 0.15s ease;
        }

        .stock-table-row:hover {
            background: #f9fafb;
        }

        .stock-cell-center {
            display: grid;
            justify-items: center;
            text-align: center;
        }

        .stock-cell-text {
            min-width: 0;
        }

        .stock-cell-text span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 1200px) {
            .stock-stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .stock-stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@stop
@section('layout')
    <div class="content-wrapper" x-data="productStockPage">
        @include('admin::shared.header', [
            'title' => 'Stock Inventory',
            'header_name' => 'Stock Inventory',
        ])
        <div class="content-body">
            {{-- Stat cards --}}
            <div class="stock-stats-grid">
                <div class="stock-stat-card">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i data-feather="box" class="w-4 h-4 text-blue-500"></i>
                    </div>
                    <div>
                        <div class="text-[11px] text-gray-400 uppercase tracking-wide">Total Items</div>
                        <div class="text-xl font-bold text-gray-700" x-text="stats.total ?? '-'"></div>
                    </div>
                </div>
                <div class="stock-stat-card is-danger">
                    <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center">
                        <i data-feather="alert-circle" class="w-4 h-4 text-red-500"></i>
                    </div>
                    <div>
                        <div class="text-[11px] text-red-400 uppercase tracking-wide">Out of Stock</div>
                        <div class="text-xl font-bold text-red-600" x-text="stats.out_of_stock ?? '-'"></div>
                    </div>
                </div>
                <div class="stock-stat-card is-warning">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center">
                        <i data-feather="alert-triangle" class="w-4 h-4 text-amber-500"></i>
                    </div>
                    <div>
                        <div class="text-[11px] text-amber-500 uppercase tracking-wide">Low Stock</div>
                        <div class="text-xl font-bold text-amber-600" x-text="stats.low_stock ?? '-'"></div>
                    </div>
                </div>
                <div class="stock-stat-card is-success">
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <i data-feather="check-circle" class="w-4 h-4 text-emerald-500"></i>
                    </div>
                    <div>
                        <div class="text-[11px] text-emerald-500 uppercase tracking-wide">In Stock</div>
                        <div class="text-xl font-bold text-emerald-600" x-text="stats.in_stock ?? '-'"></div>
                    </div>
                </div>
            </div>

            {{-- Tabs --}}
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
            stats: { total: null, out_of_stock: null, low_stock: null, in_stock: null },
            init() {
                this.inventoryTable.init();
                this.loadStats();
                feather.replace();
            },
            loadStats() {
                Axios.get("{{ route('admin-product-stock-summary') }}").then(res => {
                    this.stats = res.data;
                });
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
                        if (res) {
                            this.inventoryTable.reload();
                            this.loadStats();
                        }
                    }
                });
            },
        }));
    </script>
@stop
