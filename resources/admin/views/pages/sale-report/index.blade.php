@extends('admin::shared.layout')
@section('style')
    <style>
        .sale-stats-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 16px;
        }

        .sale-stat-card {
            align-items: center;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            display: flex;
            gap: 12px;
            min-height: 80px;
            padding: 14px 16px;
        }

        .sale-report-toolbar {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: space-between;
        }

        .sale-report-filter {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .sale-report-actions {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-end;
        }

        .sale-report-scroll {
            overflow-x: auto;
            width: 100%;
        }

        .sale-report-grid {
            align-items: center;
            column-gap: 12px;
            display: grid;
            grid-template-columns: 120px minmax(200px, 1.5fr) 80px 120px minmax(130px, 1fr) 130px 110px 80px;
            min-width: 1050px;
            width: 100%;
        }

        .sale-report-head {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 13px;
            font-weight: 700;
            height: 44px;
            padding: 0 12px;
        }

        .sale-report-row {
            border-bottom: 1px solid #f3f4f6;
            min-height: 68px;
            padding: 0 12px;
            transition: background-color 0.15s ease;
        }

        .sale-report-row:hover {
            background: #f9fafb;
        }

        .sale-report-center {
            display: grid;
            justify-items: center;
            text-align: center;
        }

        .sale-report-text {
            min-width: 0;
        }

        .sale-report-text span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 1200px) {
            .sale-stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .sale-stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@stop
@section('layout')
    <div class="content-wrapper" x-data="saleReportPage">
        @include('admin::shared.header', [
            'title'       => 'Sale Report',
            'header_name' => 'Sale Report',
        ])
        <div class="content-body">

            {{-- Stat cards --}}
            <div class="sale-stats-grid">
                <div class="sale-stat-card">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                        <i data-feather="bar-chart-2" class="w-5 h-5 text-blue-500"></i>
                    </div>
                    <div>
                        <div class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Total Sales</div>
                        <div class="text-xl font-bold text-blue-600"
                            x-text="'$' + (stats.total_sales ?? 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                        </div>
                        <div class="text-[11px] text-gray-400 mt-0.5"
                            x-text="(stats.total_orders ?? 0) + ' orders'"></div>
                    </div>
                </div>
                <div class="sale-stat-card">
                    <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center shrink-0">
                        <i data-feather="dollar-sign" class="w-5 h-5 text-red-500"></i>
                    </div>
                    <div>
                        <div class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Sub Total</div>
                        <div class="text-xl font-bold text-red-500"
                            x-text="'$' + (stats.total_cost ?? 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                        </div>
                        <div class="text-[11px] text-gray-400 mt-0.5">before shipping &amp; discount</div>
                    </div>
                </div>
                <div class="sale-stat-card">
                    <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center shrink-0">
                        <i data-feather="package" class="w-5 h-5 text-purple-500"></i>
                    </div>
                    <div>
                        <div class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Products Sold</div>
                        <div class="text-xl font-bold text-purple-600"
                            x-text="(stats.products_sold ?? 0).toLocaleString()"></div>
                        <div class="text-[11px] text-gray-400 mt-0.5">total units</div>
                    </div>
                </div>
                <div class="sale-stat-card">
                    <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center shrink-0">
                        <i data-feather="archive" class="w-5 h-5 text-orange-500"></i>
                    </div>
                    <div>
                        <div class="text-[11px] text-gray-400 uppercase tracking-wide mb-0.5">Stock on Hand</div>
                        <div class="text-xl font-bold text-orange-500"
                            x-text="(stats.stock_on_hand ?? 0).toLocaleString()"></div>
                        <div class="text-[11px] text-gray-400 mt-0.5">units in warehouse</div>
                    </div>
                </div>
            </div>

            {{-- Toolbar --}}
            <div class="content-tab h-auto! py-2 flex-wrap gap-y-2 sale-report-toolbar">
                <div class="content-tab-wrapper">
                    <span class="title !text-gray-600">
                        @lang('form.total') <span x-text="table?.paginate?.totalItems"></span>
                    </span>
                </div>
                <div class="content-action-button sale-report-actions">
                    <div class="filter sale-report-filter">
                        <div class="form-row search-inline">
                            <input type="text" x-model="formFilter.search"
                                placeholder="Order#, customer, product..."
                                autocomplete="off" @keydown.enter="onFilter()">
                            <button @click="onFilter()"><i data-feather="search"></i></button>
                        </div>
                        <div class="form-row gap-1">
                            <span class="text-xs text-gray-400 whitespace-nowrap">From</span>
                            <input type="date" x-model="formFilter.from_date" class="text-sm text-gray-600"
                                @change="onFilter()">
                        </div>
                        <div class="form-row gap-1">
                            <span class="text-xs text-gray-400 whitespace-nowrap">To</span>
                            <input type="date" x-model="formFilter.to_date" class="text-sm text-gray-600"
                                @change="onFilter()">
                        </div>
                        <div class="form-row">
                            <select x-model="formFilter.status" class="text-sm text-gray-600"
                                @change="onFilter()">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="shipping">Shipping</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <button @click="onExport()" class="btn-create">
                        <i data-feather="download"></i>
                        <span class="uppercase">Export XLSX</span>
                    </button>
                    <button @click="onReset()">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="table">
                <template x-if="table?.loading">
                    @include('admin::components.progress-bar', ['top' => true]);
                </template>
                <template x-if="!table.loading && !table?.empty()">
                    <div class="table-wrapper">
                        <div class="sale-report-scroll">
                            <div class="sale-report-grid sale-report-head">
                                <div>Order#</div>
                                <div>Product Name</div>
                                <div class="sale-report-center">Total QTY</div>
                                <div class="sale-report-center">Order Date</div>
                                <div>Customer</div>
                                <div class="sale-report-center">Status</div>
                                <div class="sale-report-center">Price</div>
                                <div class="sale-report-center">Actions</div>
                            </div>
                        </div>
                        <div class="table-body w-full! p-0!">
                            <template x-for="(item, index) in table.data" :key="`sale-${item.id}-${index}`">
                                <div class="sale-report-scroll">
                                    <div class="sale-report-grid sale-report-row">

                                        {{-- Order# --}}
                                        <div class="sale-report-text">
                                            <span class="text-xs font-mono font-semibold text-gray-700"
                                                x-text="item.order_no ?? '-'"></span>
                                        </div>

                                        {{-- Product Name --}}
                                        <div class="sale-report-text">
                                            <span class="text-sm font-medium text-gray-700"
                                                x-text="item.product_name ?? '-'"></span>
                                            <span class="text-xs text-gray-400"
                                                x-text="item.variation_name ? item.variation_name + (item.product_sku ? ' · ' + item.product_sku : '') : (item.product_sku ?? '')"></span>
                                        </div>

                                        {{-- Total QTY --}}
                                        <div class="sale-report-center">
                                            <span class="text-sm font-semibold text-gray-700"
                                                x-text="item.quantity ?? 0"></span>
                                        </div>

                                        {{-- Order Date --}}
                                        <div class="sale-report-center">
                                            <div class="flex flex-col items-center gap-0">
                                                <span class="text-xs text-gray-600"
                                                    x-text="item.order_date ? moment(item.order_date).format('DD MMM YYYY') : '-'"></span>
                                                <span class="text-[11px] text-gray-400"
                                                    x-text="item.order_date ? moment(item.order_date).fromNow() : ''"></span>
                                            </div>
                                        </div>

                                        {{-- Customer --}}
                                        <div class="sale-report-text">
                                            <span class="text-sm text-gray-700"
                                                x-text="item.recipient_name ?? '-'"></span>
                                            <span class="text-xs text-gray-400"
                                                x-text="item.recipient_phone ?? ''"></span>
                                        </div>

                                        {{-- Status --}}
                                        <div class="sale-report-center">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold"
                                                :class="{
                                                    'bg-gray-100 text-gray-600':     item.status === 'pending',
                                                    'bg-blue-100 text-blue-700':     item.status === 'confirmed',
                                                    'bg-amber-100 text-amber-700':   item.status === 'shipping',
                                                    'bg-emerald-100 text-emerald-700': item.status === 'completed',
                                                    'bg-red-100 text-red-600':       item.status === 'cancelled',
                                                }"
                                                x-text="item.status ? (item.status.charAt(0).toUpperCase() + item.status.slice(1)) : '-'">
                                            </span>
                                        </div>

                                        {{-- Price (line total) --}}
                                        <div class="sale-report-center">
                                            <span class="text-sm font-semibold text-gray-700"
                                                x-text="'$' + Number(item.line_total ?? 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                                            </span>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="sale-report-center">
                                            <a :href="`{{ url('admin/order/list/detail') }}?id=${item.order_id}`"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-600 border border-blue-200 hover:bg-blue-100 transition-colors no-underline">
                                                <i data-feather="eye" class="w-3 h-3"></i>
                                                View
                                            </a>
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
                    @component('admin::components.empty', ['name' => 'No sales', 'msg' => 'No sale records found for the selected filters.']) @endcomponent
                </template>
            </div>

        </div>
    </div>
@stop

@section('script')
    <script type="module">
        Alpine.data('saleReportPage', () => ({
            table: new Table("{{ route('admin-sale-report-data') }}"),
            stats: {
                total_sales:   0,
                total_cost:    0,
                total_orders:  0,
                products_sold: 0,
                stock_on_hand: 0,
            },
            formFilter: new FormGroup({
                search:    ['', []],
                from_date: ['', []],
                to_date:   ['', []],
                status:    ['', []],
            }),
            init() {
                this.table.init();
                this.loadStats();
                feather.replace();
            },
            loadStats() {
                const params = new URLSearchParams(this.formFilter.value()).toString();
                Axios.get(`{{ route('admin-sale-report-summary') }}?${params}`).then(res => {
                    this.stats = res.data;
                });
            },
            onFilter() {
                this.table.init(this.formFilter.value());
                this.loadStats();
            },
            onReset() {
                this.formFilter.reset();
                this.table.reset();
                this.loadStats();
            },
            onExport() {
                const params = new URLSearchParams(this.formFilter.value()).toString();
                window.location.href = `{{ route('admin-sale-report-export') }}?${params}`;
            },
        }));
    </script>
@stop
