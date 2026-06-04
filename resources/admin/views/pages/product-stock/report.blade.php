@extends('admin::shared.layout')
@section('style')
    <style>
        .stock-report-toolbar {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: space-between;
        }

        .stock-report-actions {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-end;
        }

        .stock-report-filter {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .stock-report-scroll {
            overflow-x: auto;
            width: 100%;
        }

        .stock-report-grid {
            align-items: center;
            column-gap: 12px;
            display: grid;
            grid-template-columns: 56px minmax(220px, 1.4fr) minmax(180px, 1fr) 100px 100px 110px 145px 90px 170px;
            min-width: 1120px;
            width: 100%;
        }

        .stock-report-head {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 13px;
            font-weight: 700;
            height: 44px;
            padding: 0 12px;
        }

        .stock-report-row {
            border-bottom: 1px solid #e5e7eb;
            min-height: 68px;
            padding: 0 12px;
        }

        .stock-report-row:hover {
            background: #f9fafb;
        }

        .stock-report-center {
            display: grid;
            justify-items: center;
            text-align: center;
        }

        .stock-report-text {
            min-width: 0;
        }

        .stock-report-text span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
@stop
@section('layout')
    <div class="content-wrapper" x-data="stockReportPage">
        @include('admin::shared.header', [
            'title' => 'Stock Report',
            'header_name' => 'Stock Report',
        ])
        <div class="content-body">
            <div class="content-tab h-auto! py-2 flex-wrap gap-y-2 stock-report-toolbar">
                <div class="content-tab-wrapper">
                    <span class="title !text-gray-600">
                        @lang('form.total') <span x-text="table?.paginate?.totalItems"></span>
                    </span>
                </div>
                <div class="content-action-button stock-report-actions">
                    <div class="filter stock-report-filter">
                        <div class="form-row search-inline">
                            <input type="text" x-model="formFilter.search" placeholder="Search product or SKU..."
                                autocomplete="off" @keydown.enter="onFilter()">
                            <button @click="onFilter()"><i data-feather="search"></i></button>
                        </div>
                        <div class="form-row gap-1">
                            <span class="text-xs text-gray-400 whitespace-nowrap">From</span>
                            <input type="date" x-model="formFilter.from_date" class="text-sm text-gray-600" @change="onFilter()">
                        </div>
                        <div class="form-row gap-1">
                            <span class="text-xs text-gray-400 whitespace-nowrap">To</span>
                            <input type="date" x-model="formFilter.to_date" class="text-sm text-gray-600" @change="onFilter()">
                        </div>
                        <div class="form-row">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-600 cursor-pointer select-none">
                                <input type="checkbox" x-model="formFilter.low_stock_only" class="accent-blue-600" @change="onFilter()">
                                Low Stock Only
                            </label>
                        </div>
                        <div class="form-row" x-show="formFilter.low_stock_only" x-cloak>
                            <span class="text-xs text-gray-400 whitespace-nowrap">Threshold</span>
                            <input type="number" min="0" x-model="formFilter.threshold" class="w-14 text-sm text-gray-600 text-center" @change="onFilter()">
                        </div>
                    </div>
                    <button @click="onExport()" class="btn-create">
                        <i data-feather="download"></i>
                        <span class="uppercase">Export XLSX</span>
                    </button>
                    <a href="{{ route('admin-product-stock-list') }}"
                        class="flex items-center gap-1.5 h-[35px] px-3 rounded text-xs font-medium bg-gray-100 text-gray-600 border border-gray-300 hover:bg-gray-200 transition-colors no-underline uppercase">
                        <i data-feather="arrow-left"></i>
                        Back
                    </a>
                    <button @click="onReset()">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>

            <div class="table">
                <template x-if="table?.loading">
                    @include('admin::components.progress-bar', ['top' => true]);
                </template>
                <template x-if="!table.loading && !table?.empty()">
                    <div class="table-wrapper">
                        <div class="stock-report-scroll">
                            <div class="stock-report-grid stock-report-head">
                                <div class="stock-report-center">No</div>
                                <div>Product</div>
                                <div>Variation</div>
                                <div class="stock-report-center">On Hand</div>
                                <div class="stock-report-center">Reserved</div>
                                <div class="stock-report-center">Available</div>
                                <div class="stock-report-center">Status</div>
                                <div class="stock-report-center">Moves</div>
                                <div class="stock-report-center">Latest Movement</div>
                            </div>
                        </div>
                        <div class="table-body w-full! p-0!">
                            <template x-for="(item, index) in table.data" :key="`report-${item.id}-${index}`">
                                <div class="stock-report-scroll">
                                    <div class="stock-report-grid stock-report-row border-l-4"
                                        :class="(item.stock_available ?? 0) <= 0
                                            ? 'border-l-red-400'
                                            : ((item.stock_available ?? 0) <= 5 ? 'border-l-amber-400' : 'border-l-emerald-400')">
                                        <div class="stock-report-center text-gray-500">
                                            <span class="text-sm" x-text="index + 1"></span>
                                        </div>
                                        <div class="stock-report-text text-gray-600">
                                            <span class="text-sm font-medium" x-text="item.product_name_en ?? '-'"></span>
                                            <span class="text-xs text-gray-400" x-text="item.product_sku ?? '-'"></span>
                                        </div>
                                        <div class="stock-report-text text-gray-600">
                                            <span class="text-sm font-medium" x-text="item.variation_name ?? 'Main Product'"></span>
                                            <span class="text-xs text-gray-400" x-text="item.variation_sku ?? '-'"></span>
                                        </div>
                                        <div class="stock-report-center text-gray-700">
                                            <span class="text-sm font-medium" x-text="item.stock_on_hand ?? 0"></span>
                                        </div>
                                        <div class="stock-report-center text-gray-500">
                                            <span class="text-sm" x-text="item.stock_reserved ?? 0"></span>
                                        </div>
                                        <div class="stock-report-center">
                                            <span class="text-sm font-bold"
                                                :class="(item.stock_available ?? 0) <= 0
                                                    ? 'text-red-500'
                                                    : ((item.stock_available ?? 0) <= 5 ? 'text-amber-500' : 'text-emerald-600')"
                                                x-text="item.stock_available ?? 0"></span>
                                        </div>
                                        <div class="stock-report-center">
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold"
                                                :class="(item.stock_available ?? 0) <= 0
                                                    ? 'bg-red-100 text-red-600'
                                                    : ((item.stock_available ?? 0) <= 5 ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-700')"
                                                x-text="(item.stock_available ?? 0) <= 0
                                                    ? 'Out of Stock'
                                                    : ((item.stock_available ?? 0) <= 5 ? 'Low Stock' : 'In Stock')">
                                            </span>
                                        </div>
                                        <div class="stock-report-center text-gray-700">
                                            <span class="text-sm" x-text="item.movement_count ?? 0"></span>
                                        </div>
                                        <div class="stock-report-center text-gray-500">
                                            <span class="text-xs text-center"
                                                x-text="item.latest_stock_history_at ? moment(item.latest_stock_history_at).format('MMM DD, YYYY HH:mm') : '-'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="table-footer">
                            @include('admin::components.pagination')
                        </div>
                    </div>
                </template>
                <template x-if="table && table?.empty()">
                    @component('admin::components.empty', ['name' => 'No report data', 'msg' => 'No stock report records found']) @endcomponent
                </template>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script type="module">
        Alpine.data('stockReportPage', () => ({
            table: new Table("{{ route('admin-product-stock-report-data') }}"),
            formFilter: new FormGroup({
                search: ['', []],
                from_date: ['', []],
                to_date: ['', []],
                low_stock_only: [false, []],
                threshold: [5, []],
            }),
            init() {
                this.table.init(this.formFilter.value());
                feather.replace();
            },
            onFilter() {
                this.table.init(this.formFilter.value());
            },
            onReset() {
                this.formFilter.reset();
                this.formFilter.low_stock_only = false;
                this.formFilter.threshold = 5;
                this.table.init(this.formFilter.value());
            },
            onExport() {
                const params = new URLSearchParams(this.formFilter.value()).toString();
                window.location.href = `{{ route('admin-product-stock-report-export') }}?${params}`;
            },
        }));
    </script>
@stop
