<div class="table">
    <template x-if="summaryTable?.loading">
        @include('admin::components.progress-bar', ['top' => true]);
    </template>
    <template x-if="!summaryTable.loading && !summaryTable?.empty()">
        <div class="table-wrapper">
            <div class="stock-table-scroll">
                <div class="stock-summary-grid stock-table-head">
                    <div class="stock-cell-center">No</div>
                    <div>Date</div>
                    <div>Product</div>
                    <div class="stock-cell-center">Type</div>
                    <div class="stock-cell-center">Change</div>
                    <div class="stock-cell-center">Before → After</div>
                </div>
            </div>
            <div class="table-body !w-full !p-0">
                <template x-for="(item, index) in summaryTable.data" :key="`hist-${item.id}-${index}`">
                    <div class="stock-table-scroll">
                        <div class="stock-summary-grid stock-table-row">
                            <div class="stock-cell-center text-gray-400">
                                <span class="text-sm" x-text="index + 1"></span>
                            </div>
                            <div class="stock-cell-text text-gray-500">
                                <div class="flex flex-col gap-0">
                                    <span class="text-xs" x-text="item.created_at ? moment(item.created_at).format('MMM DD, YYYY') : '-'"></span>
                                    <span class="text-[11px] text-gray-400" x-text="item.created_at ? moment(item.created_at).format('HH:mm') : ''"></span>
                                </div>
                            </div>
                            <div class="stock-cell-text text-gray-600">
                                <div class="flex flex-col gap-0">
                                    <span class="text-sm font-medium text-gray-700" x-text="item.product_name_en ?? '-'"></span>
                                    <span class="text-xs text-gray-400"
                                        x-text="item.variation_name ? item.variation_name : 'Main Product'"></span>
                                </div>
                            </div>
                            <div class="stock-cell-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold"
                                    :class="{
                                        'bg-red-100 text-red-700':    item.transaction_type === 'sale',
                                        'bg-emerald-100 text-emerald-700': item.transaction_type === 'return',
                                        'bg-purple-100 text-purple-700':   item.transaction_type === 'adjustment',
                                    }"
                                    x-text="item.transaction_type === 'sale'
                                        ? 'Sale'
                                        : (item.transaction_type === 'return'
                                            ? 'Return'
                                            : 'Adjustment')">
                                </span>
                            </div>
                            <div class="stock-cell-center">
                                <span class="text-sm font-bold"
                                    :class="(item.stock_after - item.stock_before) >= 0 ? 'text-emerald-600' : 'text-red-500'"
                                    x-text="((item.stock_after - item.stock_before) >= 0 ? '+' : '') + (item.stock_after - item.stock_before)">
                                </span>
                            </div>
                            <div class="stock-cell-center">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-sm text-gray-400" x-text="item.stock_before"></span>
                                    <span class="text-gray-300 text-xs">→</span>
                                    <span class="text-sm font-semibold text-gray-700" x-text="item.stock_after"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div class="table-footer">
                <div x-data="{ table: summaryTable }">
                    @include('admin::components.pagination')
                </div>
            </div>
        </div>
    </template>
    <template x-if="summaryTable && summaryTable?.empty()">
        @component('admin::components.empty', ['name' => 'No history', 'msg' => 'No stock history records found yet.']) @endcomponent
    </template>
</div>
