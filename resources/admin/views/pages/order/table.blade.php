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
                                <div class="w-20/100 text-sm font-bold text-gray-500 flex items-center">Order No</div>
                                <div class="w-20/100 text-sm font-bold text-gray-500 flex items-center">Customer</div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">Items</div>
                                <div class="w-15/100 text-sm font-bold text-gray-500 grid place-items-center">Grand Total</div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">Payment</div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">Status</div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center"></div>
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
                                <div class="w-20/100 flex items-center text-gray-700">
                                    <span class="text-sm font-medium" x-text="item.order_no"></span>
                                </div>
                                <div class="w-20/100 flex items-center text-gray-700">
                                    <div class="flex flex-col">
                                        <span class="text-sm" x-text="item.user?.name ?? '-'"></span>
                                        <span class="text-xs text-gray-400" x-text="item.user?.phone ?? '-'"></span>
                                    </div>
                                </div>
                                <div class="w-10/100 grid place-items-center text-gray-700">
                                    <span class="text-sm" x-text="item.items_count ?? 0"></span>
                                </div>
                                <div class="w-15/100 grid place-items-center text-gray-700">
                                    <span class="text-sm font-medium" x-text="'$' + Number(item.grand_total ?? 0).toFixed(2)"></span>
                                </div>
                                <div class="w-10/100 grid place-items-center text-gray-700">
                                    <span class="text-xs uppercase" x-text="item.payment_status ?? '-'"></span>
                                </div>
                                <div class="w-10/100 grid place-items-center text-gray-700">
                                    <span class="text-xs uppercase" x-text="item.status ?? '-'"></span>
                                </div>
                                <div class="w-10/100 grid place-items-center">
                                    <select class="text-xs border border-gray-300 rounded px-1 py-1"
                                        @change="onUpdateStatus(item.id, $event.target.value)">
                                        <option value="pending" :selected="item.status === 'pending'">Pending</option>
                                        <option value="confirmed" :selected="item.status === 'confirmed'">Confirmed</option>
                                        <option value="shipping" :selected="item.status === 'shipping'">Shipping</option>
                                        <option value="completed" :selected="item.status === 'completed'">Completed</option>
                                        <option value="cancelled" :selected="item.status === 'cancelled'">Cancelled</option>
                                    </select>
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
        @component('admin::components.empty', ['name' => 'No orders', 'msg' => 'No order found']) @endcomponent
    </template>
</div>

