<div class="table">
    <template x-if="inventoryTable?.loading">
        @include('admin::components.progress-bar', ['top' => true]);
    </template>
    <template x-if="!inventoryTable.loading && !inventoryTable?.empty()">
        <div class="table-wrapper">
            <div class="stock-table-scroll">
                <div class="stock-inventory-grid stock-table-head">
                    <div class="stock-cell-center">No</div>
                    <div>Item</div>
                    <div class="stock-cell-center flex flex-col gap-0">
                        <span>On Hand</span>
                        <span class="text-[10px] font-normal text-gray-400">in warehouse</span>
                    </div>
                    <div class="stock-cell-center flex flex-col gap-0">
                        <span>Reserved</span>
                        <span class="text-[10px] font-normal text-gray-400">pending orders</span>
                    </div>
                    <div class="stock-cell-center flex flex-col gap-0">
                        <span>Available</span>
                        <span class="text-[10px] font-normal text-gray-400">can be sold</span>
                    </div>
                    <div class="stock-cell-center">Status</div>
                    <div class="stock-cell-center">Last Movement</div>
                    <div class="stock-cell-center">Action</div>
                </div>
            </div>
            <div class="table-body !w-full !p-0">
                <template x-for="(item, index) in inventoryTable.data" :key="`item-${item.id}-${index}`">
                    <div class="stock-table-scroll">
                        <div class="stock-inventory-grid stock-table-row border-l-4"
                            :class="(item.stock_available ?? 0) <= 0
                                ? 'border-l-red-400'
                                : ((item.stock_available ?? 0) <= 5 ? 'border-l-amber-400' : 'border-l-emerald-400')">
                            <div class="stock-cell-center text-gray-400">
                                <span class="text-sm" x-text="index + 1"></span>
                            </div>
                            <div class="stock-cell-text text-gray-600">
                                <div>
                                    <span class="text-sm font-semibold text-gray-700" x-text="item.product?.name_en ?? '-'"></span>
                                    <span class="text-xs text-gray-400"
                                        x-text="`${item.product?.sku ?? '-'}${item.product_variation_id ? ' • ' + (item.variation?.name ?? 'Variant') : ' • Main Product'}`"></span>
                                </div>
                            </div>
                            <div class="stock-cell-center">
                                <span class="text-sm font-medium text-gray-700" x-text="item.stock_on_hand ?? 0"></span>
                            </div>
                            <div class="stock-cell-center">
                                <span class="text-sm text-gray-500" x-text="item.stock_reserved ?? 0"></span>
                            </div>
                            <div class="stock-cell-center">
                                <span class="text-base font-bold"
                                    :class="(item.stock_available ?? 0) <= 0
                                        ? 'text-red-500'
                                        : ((item.stock_available ?? 0) <= 5 ? 'text-amber-500' : 'text-emerald-600')"
                                    x-text="item.stock_available ?? 0"></span>
                            </div>
                            <div class="stock-cell-center">
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
                            <div class="stock-cell-center text-gray-500">
                                <div class="flex flex-col items-center gap-0.5">
                                    <span class="text-xs"
                                        x-text="(item.latest_stock_history_at ?? item.updated_at)
                                            ? moment(item.latest_stock_history_at ?? item.updated_at).format('MMM DD, YYYY HH:mm')
                                            : '-'"></span>
                                    <span class="text-[11px] text-gray-400"
                                        x-text="(item.latest_stock_history_at ?? item.updated_at)
                                            ? moment(item.latest_stock_history_at ?? item.updated_at).fromNow()
                                            : ''"></span>
                                </div>
                            </div>
                            <div class="stock-cell-center">
                                <button
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-600 border border-blue-200 hover:bg-blue-100 transition-colors"
                                    @click="openAdjustDialog(item)">
                                    Adjust
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div class="table-footer">
                <div x-data="{ table: inventoryTable }">
                    @include('admin::components.pagination')
                </div>
            </div>
        </div>
    </template>
    <template x-if="inventoryTable && inventoryTable?.empty()">
        @component('admin::components.empty', ['name' => 'No stock', 'msg' => 'No stock records found']) @endcomponent
    </template>
</div>
