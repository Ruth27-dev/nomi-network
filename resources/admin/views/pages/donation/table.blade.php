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
                                <div class="w-20/100 text-sm font-bold text-gray-500 flex items-center">Tran ID</div>
                                <div class="w-20/100 text-sm font-bold text-gray-500 flex items-center">Donor</div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">Type</div>
                                <div class="w-15/100 text-sm font-bold text-gray-500 grid place-items-center">Amount</div>
                                <div class="w-15/100 text-sm font-bold text-gray-500 grid place-items-center">Payment</div>
                                <div class="w-15/100 text-sm font-bold text-gray-500 grid place-items-center">Date</div>
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
                                    <span class="text-xs font-medium" x-text="item.tran_id"></span>
                                </div>
                                <div class="w-20/100 flex items-center text-gray-700">
                                    <div class="flex flex-col">
                                        <span class="text-sm" x-text="(item.firstname ?? '') + ' ' + (item.lastname ?? '')"></span>
                                        <span class="text-xs text-gray-400" x-text="item.email ?? item.user?.phone ?? '-'"></span>
                                    </div>
                                </div>
                                <div class="w-10/100 grid place-items-center text-gray-700">
                                    <span class="text-xs uppercase" x-text="item.donation_type ?? '-'"></span>
                                </div>
                                <div class="w-15/100 grid place-items-center text-gray-700">
                                    <span class="text-sm font-medium" x-text="'$' + Number(item.amount ?? 0).toFixed(2)"></span>
                                </div>
                                <div class="w-15/100 grid place-items-center">
                                    <span class="text-xs uppercase px-2 py-1 rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-700': item.payment_status === 'paid',
                                            'bg-yellow-100 text-yellow-700': item.payment_status === 'pending',
                                            'bg-red-100 text-red-700': item.payment_status === 'failed',
                                            'bg-blue-100 text-blue-700': item.payment_status === 'refunded',
                                        }"
                                        x-text="item.payment_status ?? '-'">
                                    </span>
                                </div>
                                <div class="w-15/100 grid place-items-center text-gray-500">
                                    <span class="text-xs" x-text="item.created_at ? new Date(item.created_at).toLocaleDateString() : '-'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div class="flex justify-end items-center px-4 py-3 border-t border-gray-200 bg-gray-50">
                <span class="text-sm text-gray-500 mr-2">Total Paid Amount:</span>
                <span class="text-sm font-bold text-green-700" x-text="'$' + (table.otherData?.total_amount ?? '0.00')"></span>
            </div>
            <div class="table-footer">
                @include('admin::components.pagination')
            </div>
        </div>
    </template>
    <template x-if="table && table?.empty()">
        @component('admin::components.empty', ['name' => 'No donations', 'msg' => 'No donation found']) @endcomponent
    </template>
</div>
