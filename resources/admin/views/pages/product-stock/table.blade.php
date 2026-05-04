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
                                <div class="w-15/100 text-sm font-bold text-gray-500 flex items-center">Variation SKU</div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">On Hand</div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">Reserved</div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">Available</div>
                                <div class="w-15/100 text-sm font-bold text-gray-500 grid place-items-center">Updated</div>
                                <div class="w-5/100 text-sm font-bold text-gray-500 grid place-items-center"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-body !w-full !p-0">
                <template x-for="(item, index) in table.data">
                    <div class="w-full flex gap-3 h-[68px]">
                        <div class="flex-auto border-b border-gray-200">
                            <div class="flex row-item h-full hover:bg-type_gray">
                                <div class="w-5/100 grid place-items-center text-gray-500">
                                    <span class="text-sm" x-text="index + 1"></span>
                                </div>
                                <div class="w-30/100 text-gray-600 flex items-center pr-2">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium" x-text="item.product?.name_en ?? '-'"></span>
                                        <span class="text-xs text-gray-400" x-text="item.product?.sku ?? '-'"></span>
                                    </div>
                                </div>
                                <div class="w-15/100 text-gray-600 flex items-center">
                                    <span class="text-sm" x-text="item.variation?.sku ?? '-'"></span>
                                </div>
                                <div class="w-10/100 grid place-items-center text-gray-700">
                                    <span class="text-sm font-medium" x-text="item.stock_on_hand ?? 0"></span>
                                </div>
                                <div class="w-10/100 grid place-items-center text-gray-700">
                                    <span class="text-sm" x-text="item.stock_reserved ?? 0"></span>
                                </div>
                                <div class="w-10/100 grid place-items-center text-gray-700">
                                    <span class="text-sm" x-text="item.stock_available ?? 0"></span>
                                </div>
                                <div class="w-15/100 grid place-items-center text-gray-500">
                                    <span class="text-xs" x-text="item.updated_at ?? '-'"></span>
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
            <div class="table-footer">
                @include('admin::components.pagination')
            </div>
        </div>
    </template>
    <template x-if="table && table?.empty()">
        @component('admin::components.empty', ['name' => 'No stock', 'msg' => 'No stock records found']) @endcomponent
    </template>
</div>

