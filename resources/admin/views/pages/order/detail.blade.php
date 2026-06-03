@extends('admin::shared.layout')
@section('layout')
<div class="content-wrapper" x-data="orderDetail">
    @include('admin::shared.header', [
        'title' => 'Order Detail',
        'header_name' => 'Order Detail',
    ])

    <div class="content-body">
        <template x-if="loading">
            @include('admin::components.progress-bar', ['top' => true])
        </template>

        <template x-if="!loading && order">
            <div class="max-w-5xl mx-auto px-4 py-6 space-y-6">

                {{-- Header row --}}
                <div class="flex items-center justify-between">
                    <div>
                        <a href="{{ route('admin-order-list') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1 mb-1">
                            <i data-feather="arrow-left" class="w-3 h-3"></i> Orders
                        </a>
                        <h2 class="text-lg font-bold text-gray-800" x-text="order.order_no"></h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                :class="{
                                    'bg-green-100 text-green-700': order.payment_status === 'paid',
                                    'bg-yellow-100 text-yellow-700': order.payment_status === 'pending',
                                    'bg-red-100 text-red-700': order.payment_status === 'failed' || order.payment_status === 'unpaid',
                                    'bg-blue-100 text-blue-700': order.payment_status === 'refunded',
                                }"
                                x-text="order.payment_status?.toUpperCase()">
                            </span>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-gray-100 text-gray-600"
                                x-text="order.status?.toUpperCase()">
                            </span>
                        </div>
                    </div>

                    {{-- Status update --}}
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">Update status:</span>
                        <select x-model="newStatus" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="shipping">Shipping</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <button @click="onUpdateStatus()"
                            class="bg-gray-800 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-gray-700">
                            Save
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-6">

                    {{-- LEFT: Items + Summary --}}
                    <div class="col-span-2 space-y-4">

                        {{-- Order Items --}}
                        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                            <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700 text-sm">Items</div>
                            <template x-for="item in order.items" :key="item.id">
                                <div class="flex items-center gap-4 px-5 py-4 border-b border-gray-50 last:border-0">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800" x-text="item.product_name"></p>
                                        <p class="text-xs text-gray-400" x-text="item.variation_name ? `Variation: ${item.variation_name}` : ''"></p>
                                        <p class="text-xs text-gray-400" x-text="`SKU: ${item.product_sku ?? '-'}`"></p>
                                    </div>
                                    <div class="text-sm text-gray-500" x-text="`x${item.quantity}`"></div>
                                    <div class="text-sm font-medium text-gray-700 w-24 text-right"
                                        x-text="`$${Number(item.unit_price).toFixed(2)}`"></div>
                                    <div class="text-sm font-semibold text-gray-800 w-24 text-right"
                                        x-text="`$${Number(item.line_total).toFixed(2)}`"></div>
                                </div>
                            </template>

                            {{-- Totals --}}
                            <div class="px-5 py-4 bg-gray-50 space-y-2">
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span>Subtotal</span>
                                    <span x-text="`$${Number(order.sub_total ?? 0).toFixed(2)}`"></span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span>Shipping</span>
                                    <span x-text="`$${Number(order.shipping_fee ?? 0).toFixed(2)}`"></span>
                                </div>
                                <div x-show="order.discount_amount > 0" class="flex justify-between text-sm text-green-600">
                                    <span>Discount</span>
                                    <span x-text="`-$${Number(order.discount_amount ?? 0).toFixed(2)}`"></span>
                                </div>
                                <div class="flex justify-between text-base font-bold text-gray-800 border-t border-gray-200 pt-2">
                                    <span>Grand Total</span>
                                    <span x-text="`$${Number(order.grand_total ?? 0).toFixed(2)}`"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Shipping Timeline --}}
                        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                            <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700 text-sm">Shipping Status</div>
                            <div class="px-5 py-5">
                                <div class="flex items-center justify-between relative">
                                    <div class="absolute top-4 left-0 right-0 h-0.5 bg-gray-200 z-0"></div>
                                    <template x-for="(step, i) in statusSteps" :key="step.key">
                                        <div class="flex flex-col items-center z-10 relative">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
                                                :class="isStepDone(step.key)
                                                    ? 'bg-gray-800 text-white'
                                                    : 'bg-white border-2 border-gray-300 text-gray-400'">
                                                <template x-if="isStepDone(step.key)">
                                                    <i data-feather="check" class="w-4 h-4"></i>
                                                </template>
                                                <template x-if="!isStepDone(step.key)">
                                                    <span x-text="i + 1"></span>
                                                </template>
                                            </div>
                                            <span class="text-xs mt-1 text-gray-500" x-text="step.label"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT: Customer + Shipping --}}
                    <div class="space-y-4">

                        {{-- Customer --}}
                        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                            <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700 text-sm">Customer</div>
                            <div class="px-5 py-4 space-y-1">
                                <p class="text-sm font-medium text-gray-800" x-text="order.user?.name ?? '-'"></p>
                                <p class="text-xs text-gray-500" x-text="order.user?.phone ?? '-'"></p>
                            </div>
                        </div>

                        {{-- Shipping Address --}}
                        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                            <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700 text-sm">Shipping Address</div>
                            <div class="px-5 py-4 space-y-1">
                                <p class="text-sm font-medium text-gray-800" x-text="order.recipient_name ?? '-'"></p>
                                <p class="text-xs text-gray-500" x-text="order.recipient_phone ?? '-'"></p>
                                <p class="text-xs text-gray-500" x-text="order.shipping_address ?? '-'"></p>
                            </div>
                        </div>

                        {{-- Shipping Method --}}
                        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                            <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700 text-sm">Shipping Method</div>
                            <div class="px-5 py-4">
                                <p class="text-sm text-gray-700" x-text="order.shipping_method_title ?? '-'"></p>
                            </div>
                        </div>

                        {{-- Payment --}}
                        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                            <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700 text-sm">Payment</div>
                            <div class="px-5 py-4 space-y-1">
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>Method</span>
                                    <span x-text="order.payment_method?.toUpperCase() ?? '-'"></span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>Status</span>
                                    <span class="font-semibold"
                                        :class="order.payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600'"
                                        x-text="order.payment_status?.toUpperCase() ?? '-'">
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Note --}}
                        <template x-if="order.note">
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                                <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700 text-sm">Note</div>
                                <div class="px-5 py-4">
                                    <p class="text-sm text-gray-600" x-text="order.note"></p>
                                </div>
                            </div>
                        </template>

                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
@stop
@section('script')
<script type="module">
    Alpine.data('orderDetail', () => ({
        order: null,
        loading: true,
        newStatus: '',
        statusSteps: [
            { key: 'pending',   label: 'Pending' },
            { key: 'confirmed', label: 'Confirmed' },
            { key: 'shipping',  label: 'Shipping' },
            { key: 'completed', label: 'Completed' },
        ],
        statusOrder: ['pending', 'confirmed', 'shipping', 'completed'],

        init() {
            const id = new URLSearchParams(location.search).get('id');
            Axios.get("{{ route('admin-order-detail') }}", { params: { id } })
                .then(res => {
                    this.order = res.data.data;
                    this.newStatus = this.order.status;
                    this.$nextTick(() => feather.replace());
                })
                .finally(() => { this.loading = false; });
        },

        isStepDone(key) {
            const current = this.statusOrder.indexOf(this.order?.status);
            const step    = this.statusOrder.indexOf(key);
            return step <= current;
        },

        onUpdateStatus() {
            this.$store.confirmDialog.open({
                data: {
                    title: "@lang('dialog.title')",
                    message: `Update order status to ${this.newStatus}?`,
                    btnClose: "@lang('dialog.button.close')",
                    btnSave: "@lang('dialog.button.save')",
                },
                afterClosed: (result) => {
                    if (!result) return;
                    Axios.post("{{ route('admin-order-status') }}", { id: this.order.id, status: this.newStatus })
                        .then(res => {
                            if (res.data.error === false) {
                                this.order.status = this.newStatus;
                                toastr.success(res.data.message, { progressBar: true, timeOut: 3000 });
                                this.$nextTick(() => feather.replace());
                            } else {
                                toastr.error(res.data.message ?? 'Something went wrong!', { progressBar: true, timeOut: 3000 });
                            }
                        }).catch(e => {
                            toastr.error(e?.response?.data?.message ?? 'Something went wrong!', { progressBar: true, timeOut: 3000 });
                        });
                }
            });
        },
    }));
</script>
@stop
