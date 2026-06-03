@extends('admin::shared.layout')
@section('layout')
<div class="content-wrapper" x-data="orderDetail">
    <div class="content-body px-6 py-6">
        <template x-if="loading">
            @include('admin::components.progress-bar', ['top' => true])
        </template>

        <template x-if="!loading && !order">
            <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                <i data-feather="alert-circle" class="w-10 h-10 mb-3"></i>
                <p class="text-sm">Order not found or failed to load.</p>
                <a href="{{ route('admin-order-list') }}" class="mt-4 text-sm text-blue-500 hover:underline">Back to Orders</a>
            </div>
        </template>

        <template x-if="!loading && order">
            <div>
                {{-- Top Header --}}
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="text-xl font-bold text-gray-900" x-text="`Order #${order.order_no}`"></h1>
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold bg-yellow-100 text-yellow-700"
                                x-text="order.status?.charAt(0).toUpperCase() + order.status?.slice(1)"></span>
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold"
                                :class="{
                                    'bg-green-100 text-green-700': order.payment_status === 'paid',
                                    'bg-red-100 text-red-700': order.payment_status === 'unpaid' || order.payment_status === 'failed',
                                    'bg-yellow-100 text-yellow-700': order.payment_status === 'pending',
                                }"
                                x-text="order.payment_status?.charAt(0).toUpperCase() + order.payment_status?.slice(1)">
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1" x-text="order.created_at ? new Date(order.created_at).toLocaleString() : ''"></p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin-order-list') }}"
                            class="text-sm px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">
                            Back to Orders
                        </a>
                    </div>
                </div>

                <div class="flex gap-6 items-start">

                    {{-- LEFT --}}
                    <div class="flex-1 space-y-4">

                        {{-- Order Details --}}
                        <div class="bg-white border border-gray-200 rounded-2xl p-6">
                            <h2 class="text-base font-bold text-gray-800 mb-4">Order Details</h2>

                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="text-left text-xs text-gray-400 font-medium pb-3">Product</th>
                                        <th class="text-right text-xs text-gray-400 font-medium pb-3">Price</th>
                                        <th class="text-right text-xs text-gray-400 font-medium pb-3">Qty</th>
                                        <th class="text-right text-xs text-gray-400 font-medium pb-3">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="item in order.items" :key="item.id">
                                        <tr class="border-b border-gray-50">
                                            <td class="py-4">
                                                <div class="flex items-center gap-3">
                                                    <img :src="item.product?.images?.[0]?.url ?? '/images/placeholder.png'"
                                                        class="w-14 h-14 rounded-lg object-cover border border-gray-100"
                                                        :alt="item.product_name">
                                                    <div>
                                                        <p class="text-sm font-semibold text-gray-800" x-text="item.product_name"></p>
                                                        <p class="text-xs text-gray-400" x-text="item.product_sku ? `SKU: ${item.product_sku}` : ''"></p>
                                                        <p class="text-xs text-gray-400" x-show="item.variation_name" x-text="`Variation: ${item.variation_name}`"></p>
                                                        <p class="text-xs text-gray-400" x-show="item.variation_sku" x-text="`Variation SKU: ${item.variation_sku}`"></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 text-right text-sm text-gray-700" x-text="`$${Number(item.unit_price).toFixed(2)}`"></td>
                                            <td class="py-4 text-right text-sm text-gray-700" x-text="item.quantity"></td>
                                            <td class="py-4 text-right text-sm font-semibold text-gray-800" x-text="`$${Number(item.line_total).toFixed(2)}`"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>

                            {{-- Totals --}}
                            <div class="mt-4 space-y-2 max-w-xs ml-auto text-sm">
                                <div class="flex justify-between text-gray-500">
                                    <span>Subtotal:</span>
                                    <span x-text="`$${Number(order.sub_total ?? 0).toFixed(2)}`"></span>
                                </div>
                                <div class="flex justify-between text-gray-500">
                                    <span>Shipping Fee:</span>
                                    <span x-text="`$${Number(order.shipping_fee ?? 0).toFixed(2)}`"></span>
                                </div>
                                <div class="flex justify-between text-gray-500">
                                    <span>Discount:</span>
                                    <span x-text="`-$${Number(order.discount_amount ?? 0).toFixed(2)}`"></span>
                                </div>
                                <div class="flex justify-between font-bold text-gray-900 border-t border-gray-200 pt-2 text-base">
                                    <span>Total:</span>
                                    <span x-text="`$${Number(order.grand_total ?? 0).toFixed(2)}`"></span>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT --}}
                    <div class="w-80 space-y-4 flex-shrink-0">

                        {{-- Customer Details --}}
                        <div class="bg-white border border-gray-200 rounded-2xl p-5">
                            <h2 class="text-base font-bold text-gray-800 mb-4">Customer Details</h2>

                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-pink-400 flex items-center justify-center text-white text-sm font-bold"
                                    x-text="order.user?.name ? order.user.name.split(' ').map(n => n[0]).join('').slice(0,2).toUpperCase() : '?'">
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800" x-text="order.user?.name ?? '-'"></p>
                                    <p class="text-xs text-gray-400" x-text="`Customer ID: #${order.user?.id ?? '-'}`"></p>
                                </div>
                            </div>

                            <div class="text-xs text-gray-500 space-y-1 mb-5">
                                <p class="font-semibold text-gray-400 uppercase tracking-wide text-[10px] mb-2">Contact Info</p>
                                <p x-show="order.user?.email" x-text="`Email: ${order.user?.email ?? ''}`"></p>
                                <p x-text="`Mobile: ${order.user?.phone ?? '-'}`"></p>
                            </div>

                            {{-- Order Status --}}
                            <div class="mb-4">
                                <label class="text-xs text-gray-400 mb-1 block">Order Status</label>
                                <div class="flex gap-2">
                                    <select x-model="newStatus" class="flex-1 text-sm border border-gray-200 rounded-lg px-3 py-2">
                                        <option value="pending">Pending</option>
                                        <option value="confirmed">Confirmed</option>
                                        <option value="shipping">Shipping</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                    <button @click="onUpdateStatus()"
                                        class="bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold px-4 py-2 rounded-lg">
                                        Update
                                    </button>
                                </div>
                            </div>

                            {{-- Payment Status --}}
                            <div>
                                <label class="text-xs text-gray-400 mb-1 block">Payment Status</label>
                                <div class="flex gap-2">
                                    <select x-model="newPaymentStatus" class="flex-1 text-sm border border-gray-200 rounded-lg px-3 py-2">
                                        <option value="unpaid">Unpaid</option>
                                        <option value="pending">Pending</option>
                                        <option value="paid">Paid</option>
                                        <option value="failed">Failed</option>
                                        <option value="refunded">Refunded</option>
                                    </select>
                                    <button @click="onUpdatePaymentStatus()"
                                        class="border border-gray-200 text-gray-600 text-sm px-4 py-2 rounded-lg hover:bg-gray-50">
                                        Apply
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Shipping Address --}}
                        <div class="bg-white border border-gray-200 rounded-2xl p-5">
                            <h2 class="text-base font-bold text-gray-800 mb-3">Shipping Address</h2>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p class="font-semibold text-gray-800" x-text="order.recipient_name ?? '-'"></p>
                                <p x-text="order.shipping_address ?? '-'"></p>
                                <p x-show="order.recipient_phone" x-text="order.recipient_phone"></p>
                                <p x-show="order.shipping_method_title"
                                    class="text-xs text-gray-400 pt-1"
                                    x-text="`via ${order.shipping_method_title}`"></p>
                            </div>
                        </div>

                        {{-- Note --}}
                        <template x-if="order.note">
                            <div class="bg-white border border-gray-200 rounded-2xl p-5">
                                <h2 class="text-base font-bold text-gray-800 mb-2">Note</h2>
                                <p class="text-sm text-gray-500" x-text="order.note"></p>
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
        newPaymentStatus: '',

        init() {
            const id = new URLSearchParams(location.search).get('id');
            if (!id) {
                this.loading = false;
                return;
            }
            Axios.get("{{ route('admin-order-detail') }}", { params: { id } })
                .then(res => {
                    this.order = res.data?.data ?? null;
                    if (this.order) {
                        this.newStatus        = this.order.status ?? 'pending';
                        this.newPaymentStatus = this.order.payment_status ?? 'unpaid';
                    }
                    this.$nextTick(() => feather.replace());
                })
                .catch(e => {
                    toastr.error(e?.response?.data?.message ?? 'Failed to load order.', { progressBar: true, timeOut: 3000 });
                })
                .finally(() => { this.loading = false; });
        },

        onUpdateStatus() {
            this.$store.confirmDialog.open({
                data: {
                    title: "@lang('dialog.title')",
                    message: `Update order status to "${this.newStatus}"?`,
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
                            } else {
                                toastr.error(res.data.message ?? 'Something went wrong!', { progressBar: true, timeOut: 3000 });
                            }
                        }).catch(e => {
                            toastr.error(e?.response?.data?.message ?? 'Something went wrong!', { progressBar: true, timeOut: 3000 });
                        });
                }
            });
        },
        onUpdatePaymentStatus() {
            this.$store.confirmDialog.open({
                data: {
                    title: "@lang('dialog.title')",
                    message: `Update payment status to "${this.newPaymentStatus}"?` +
                        (this.newPaymentStatus === 'paid' && this.order.status === 'pending'
                            ? '\n\nOrder status will also be updated to Confirmed.' : ''),
                    btnClose: "@lang('dialog.button.close')",
                    btnSave: "@lang('dialog.button.save')",
                },
                afterClosed: (result) => {
                    if (!result) return;
                    Axios.post("{{ route('admin-order-payment-status') }}", { id: this.order.id, payment_status: this.newPaymentStatus })
                        .then(res => {
                            if (res.data.error === false) {
                                this.order.payment_status = res.data.data.payment_status;
                                this.order.status         = res.data.data.status;
                                this.newStatus            = res.data.data.status;
                                toastr.success(res.data.message, { progressBar: true, timeOut: 3000 });
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
