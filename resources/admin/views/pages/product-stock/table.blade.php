<div class="table">
    <template x-if="inventoryTable?.loading">
        @include('admin::components.progress-bar', ['top' => true]);
    </template>
    <template x-if="!inventoryTable.loading && !inventoryTable?.empty()">
        <div class="table-wrapper">
            <div class="table-header">
                <div class="flex flex-col flex-auto">
                    <div class="w-full flex gap-3">
                        <div class="flex-auto border-t border-b border-gray-200 bg-gray-50">
                            <div class="flex h-11">
                                <div class="w-5/100 text-sm font-bold text-gray-500 grid place-items-center">No</div>
                                <div class="w-28/100 text-sm font-bold text-gray-500 flex items-center">Item</div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">On Hand</div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">Reserved</div>
                                <div class="w-12/100 text-sm font-bold text-gray-500 grid place-items-center">Available</div>
                                <div class="w-13/100 text-sm font-bold text-gray-500 grid place-items-center">Status</div>
                                <div class="w-15/100 text-sm font-bold text-gray-500 grid place-items-center">Last Movement</div>
                                <div class="w-7/100 text-sm font-bold text-gray-500 grid place-items-center">Action</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-body !w-full !p-0">
                <template x-for="(item, index) in inventoryTable.data" :key="`item-${item.id}-${index}`">
                    <div class="w-full flex gap-3 h-[72px]">
                        <div class="flex-auto border-b border-gray-200 border-l-4 transition-colors"
                            :class="(item.stock_available ?? 0) <= 0
                                ? 'border-l-red-400'
                                : ((item.stock_available ?? 0) <= 5 ? 'border-l-amber-400' : 'border-l-emerald-400')">
                            <div class="flex row-item h-full hover:bg-type_gray">
                                <div class="w-5/100 grid place-items-center text-gray-400">
                                    <span class="text-sm" x-text="index + 1"></span>
                                </div>
                                <div class="w-28/100 text-gray-600 flex items-center pr-2">
                                    <div class="flex flex-col gap-0.5">
                                        <span class="text-sm font-semibold text-gray-700" x-text="item.product?.name_en ?? '-'"></span>
                                        <span class="text-xs text-gray-400"
                                            x-text="`${item.product?.sku ?? '-'}${item.product_variation_id ? ' • ' + (item.variation?.name ?? 'Variant') : ' • Main Product'}`"></span>
                                    </div>
                                </div>
                                <div class="w-10/100 grid place-items-center">
                                    <span class="text-sm font-medium text-gray-700" x-text="item.stock_on_hand ?? 0"></span>
                                </div>
                                <div class="w-10/100 grid place-items-center">
                                    <span class="text-sm text-gray-500" x-text="item.stock_reserved ?? 0"></span>
                                </div>
                                <div class="w-12/100 grid place-items-center">
                                    <span class="text-base font-bold"
                                        :class="(item.stock_available ?? 0) <= 0
                                            ? 'text-red-500'
                                            : ((item.stock_available ?? 0) <= 5 ? 'text-amber-500' : 'text-emerald-600')"
                                        x-text="item.stock_available ?? 0"></span>
                                </div>
                                <div class="w-13/100 grid place-items-center">
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
                                <div class="w-15/100 grid place-items-center text-gray-500">
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
                                <div class="w-7/100 grid place-items-center">
                                    <button
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-600 border border-blue-200 hover:bg-blue-100 transition-colors"
                                        @click="openAdjustDialog(item)">
                                        Adjust
                                    </button>
                                </div>
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
