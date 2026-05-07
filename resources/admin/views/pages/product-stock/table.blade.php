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
                                <div class="w-30/100 text-sm font-bold text-gray-500 flex items-center">Product</div>
                                <div class="w-15/100 text-sm font-bold text-gray-500 flex items-center">Variation</div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">On Hand</div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">Reserved</div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">Available</div>
                                <div class="w-15/100 text-sm font-bold text-gray-500 grid place-items-center">Last Movement</div>
                                <div class="w-5/100 text-sm font-bold text-gray-500 grid place-items-center"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-body !w-full !p-0">
                <template x-for="(group, groupIndex) in groupedStocks()" :key="`group-${group.product_id}-${groupIndex}`">
                    <div class="w-full">
                        <div class="flex border-b border-gray-200 bg-gray-50/70 h-[44px]">
                            <div class="w-5/100"></div>
                            <div class="w-30/100 flex items-center pr-2">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-2 h-2 rounded-full bg-primary"></span>
                                    <span class="text-sm font-semibold text-gray-700" x-text="group.product_name"></span>
                                    <span class="text-xs text-gray-400" x-text="`(${group.product_sku})`"></span>
                                </div>
                            </div>
                            <div class="w-15/100"></div>
                            <div class="w-10/100"></div>
                            <div class="w-10/100"></div>
                            <div class="w-10/100"></div>
                            <div class="w-15/100"></div>
                            <div class="w-5/100"></div>
                        </div>
                        <template x-for="(item, index) in group.items" :key="`item-${item.id}-${index}`">
                            <div class="w-full flex gap-3 h-[68px]">
                                <div class="flex-auto border-b border-gray-200">
                                    <div class="flex row-item h-full hover:bg-type_gray">
                                        <div class="w-5/100 grid place-items-center text-gray-500">
                                            <span class="text-sm" x-text="group.start_index + index + 1"></span>
                                        </div>
                                        <div class="w-30/100 text-gray-600 flex items-center pr-2">
                                            <span class="text-xs text-gray-400">Variant item</span>
                                        </div>
                                        <div class="w-15/100 text-gray-600 flex items-center">
                                            <div class="flex items-center gap-2">
                                                <span class="text-gray-300">↳</span>
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-medium" x-text="item.variation?.name ?? (item.variation?.sku ? 'Unnamed Variant' : 'Main Product')"></span>
                                                    <span class="text-xs text-gray-400" x-text="item.variation?.sku ?? '-'"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-10/100 grid place-items-center text-gray-700">
                                            <span class="text-sm font-medium" x-text="item.stock_on_hand ?? 0"></span>
                                        </div>
                                        <div class="w-10/100 grid place-items-center text-gray-700">
                                            <span class="text-sm" x-text="item.stock_reserved ?? 0"></span>
                                        </div>
                                        <div class="w-10/100 grid place-items-center text-gray-700">
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="text-sm font-semibold" x-text="item.stock_available ?? 0"></span>
                                                <span
                                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold"
                                                    :class="(item.stock_available ?? 0) <= 0
                                                        ? 'bg-red-100 text-red-700'
                                                        : ((item.stock_available ?? 0) <= 5 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700')"
                                                    x-text="(item.stock_available ?? 0) <= 0
                                                        ? 'Out'
                                                        : ((item.stock_available ?? 0) <= 5 ? 'Low' : 'In Stock')">
                                                </span>
                                            </div>
                                        </div>
                                        <div class="w-15/100 grid place-items-center text-gray-500">
                                            <div class="flex flex-col items-center">
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
                                        <div class="w-5/100 grid place-items-center">
                                            <button class="text-blue-600 text-xs underline" @click="openAdjustDialog(item)">
                                                Adjust
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
            <div class="table-footer">
                @include('admin::components.pagination')
            </div>
        </div>
    </template>
    <template x-if="table && table?.empty()">
        @component('admin::components.empty', ['name' => 'No stock', 'msg' => 'No stock records found']) @endcomponent
    </template>
</div>
