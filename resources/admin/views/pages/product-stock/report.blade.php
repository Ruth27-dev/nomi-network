@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="stockReportPage">
        @include('admin::shared.header', [
            'title' => 'Stock Report',
            'header_name' => 'Stock Report',
        ])
        <div class="content-body">
            <div class="content-tab h-auto! py-2 flex-wrap gap-y-2">
                <div class="content-tab-wrapper">
                    <span class="title !text-gray-600">
                        @lang('form.total') <span x-text="table?.paginate?.totalItems"></span>
                    </span>
                </div>
                <div class="content-action-button flex-wrap gap-2">
                    <div class="filter flex-wrap gap-2">
                        <div class="form-row search-inline">
                            <input type="text" x-model="formFilter.search" placeholder="Search product or SKU..."
                                autocomplete="off" @keydown.enter="onFilter()">
                            <button @click="onFilter()"><i data-feather="search"></i></button>
                        </div>
                        <div class="form-row gap-1">
                            <span class="text-xs text-gray-400 whitespace-nowrap">From</span>
                            <input type="date" x-model="formFilter.from_date" class="text-sm text-gray-600">
                        </div>
                        <div class="form-row gap-1">
                            <span class="text-xs text-gray-400 whitespace-nowrap">To</span>
                            <input type="date" x-model="formFilter.to_date" class="text-sm text-gray-600">
                        </div>
                        <div class="form-row">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-600 cursor-pointer select-none">
                                <input type="checkbox" x-model="formFilter.low_stock_only" class="accent-blue-600">
                                Low Stock Only
                            </label>
                        </div>
                        <div class="form-row" x-show="formFilter.low_stock_only" x-cloak>
                            <span class="text-xs text-gray-400 whitespace-nowrap">Threshold</span>
                            <input type="number" min="0" x-model="formFilter.threshold" class="w-14 text-sm text-gray-600 text-center">
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
                        <div class="table-header">
                            <div class="flex flex-col flex-auto">
                                <div class="w-full flex gap-3">
                                    <div class="flex-auto border-t border-b border-gray-200 bg-gray-50">
                                        <div class="flex h-11">
                                            <div class="w-5/100 text-sm font-bold text-gray-500 grid place-items-center">No</div>
                                            <div class="w-25/100 text-sm font-bold text-gray-500 flex items-center">Product</div>
                                            <div class="w-15/100 text-sm font-bold text-gray-500 flex items-center">Variation</div>
                                            <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">On Hand</div>
                                            <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">Reserved</div>
                                            <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">Available</div>
                                            <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">Moves</div>
                                            <div class="w-15/100 text-sm font-bold text-gray-500 grid place-items-center">Latest Movement</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-body w-full! p-0!">
                            <template x-for="(item, index) in table.data" :key="`report-${item.id}-${index}`">
                                <div class="w-full flex gap-3 h-[68px]">
                                    <div class="flex-auto border-b border-gray-200">
                                        <div class="flex row-item h-full hover:bg-type_gray">
                                            <div class="w-5/100 grid place-items-center text-gray-500">
                                                <span class="text-sm" x-text="index + 1"></span>
                                            </div>
                                            <div class="w-25/100 text-gray-600 flex items-center">
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-medium" x-text="item.product_name_en ?? '-'"></span>
                                                    <span class="text-xs text-gray-400" x-text="item.product_sku ?? '-'"></span>
                                                </div>
                                            </div>
                                            <div class="w-15/100 text-gray-600 flex items-center">
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-medium" x-text="item.variation_name ?? 'Main Product'"></span>
                                                    <span class="text-xs text-gray-400" x-text="item.variation_sku ?? '-'"></span>
                                                </div>
                                            </div>
                                            <div class="w-10/100 grid place-items-center text-gray-700"><span class="text-sm font-medium" x-text="item.stock_on_hand ?? 0"></span></div>
                                            <div class="w-10/100 grid place-items-center text-gray-700"><span class="text-sm" x-text="item.stock_reserved ?? 0"></span></div>
                                            <div class="w-10/100 grid place-items-center text-gray-700"><span class="text-sm font-semibold" x-text="item.stock_available ?? 0"></span></div>
                                            <div class="w-10/100 grid place-items-center text-gray-700"><span class="text-sm" x-text="item.movement_count ?? 0"></span></div>
                                            <div class="w-15/100 grid place-items-center text-gray-500">
                                                <span class="text-xs"
                                                    x-text="item.latest_stock_history_at ? moment(item.latest_stock_history_at).format('MMM DD, YYYY HH:mm') : '-'"></span>
                                            </div>
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
