<div class="table">
    <template x-if="summaryTable?.loading">
        @include('admin::components.progress-bar', ['top' => true]);
    </template>
    <template x-if="!summaryTable.loading && !summaryTable?.empty()">
        <div class="table-wrapper">
            <div class="stock-table-scroll">
                <div class="stock-summary-grid stock-table-head">
                    <div class="stock-cell-center">No</div>
                    <div>Product</div>
                    <div>Variation</div>
                    <div class="stock-cell-center">Total Moves</div>
                    <div class="stock-cell-center">Latest Movement</div>
                    <div class="stock-cell-center">Type</div>
                </div>
            </div>
            <div class="table-body !w-full !p-0">
                <template x-for="(item, index) in summaryTable.data" :key="`summary-${item.product_id}-${item.product_variation_id ?? 'main'}-${index}`">
                    <div class="stock-table-scroll">
                        <div class="stock-summary-grid stock-table-row">
                            <div class="stock-cell-center text-gray-500">
                                <span class="text-sm" x-text="index + 1"></span>
                            </div>
                            <div class="stock-cell-text text-gray-600">
                                <div>
                                    <span class="text-sm font-medium" x-text="item.product_name_en ?? '-'"></span>
                                    <span class="text-xs text-gray-400" x-text="item.product_sku ?? '-'"></span>
                                </div>
                            </div>
                            <div class="stock-cell-text text-gray-600">
                                <div>
                                    <span class="text-sm font-medium" x-text="item.variation_name ?? (item.product_variation_id ? 'Unnamed Variant' : '-')"></span>
                                    <span class="text-xs text-gray-400" x-text="item.variation_sku ?? '-'"></span>
                                </div>
                            </div>
                            <div class="stock-cell-center text-gray-700">
                                <span class="text-sm font-semibold" x-text="item.movement_count ?? 0"></span>
                            </div>
                            <div class="stock-cell-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <span class="text-xs"
                                        x-text="item.latest_stock_history_at
                                            ? moment(item.latest_stock_history_at).format('MMM DD, YYYY HH:mm')
                                            : '-'"></span>
                                    <span class="text-[11px] text-gray-400"
                                        x-text="item.latest_stock_history_at
                                            ? moment(item.latest_stock_history_at).fromNow()
                                            : ''"></span>
                                </div>
                            </div>
                            <div class="stock-cell-center">
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold"
                                    :class="item.product_variation_id ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'"
                                    x-text="item.product_variation_id ? 'Variant' : 'Main'">
                                </span>
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
        @component('admin::components.empty', ['name' => 'No stock movement', 'msg' => 'No movement summary records found']) @endcomponent
    </template>
</div>
