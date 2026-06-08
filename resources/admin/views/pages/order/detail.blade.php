@extends('admin::shared.layout')
@section('style')
<style>
    .order-detail-page {
        padding: 20px;
    }

    .order-detail-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }

    .order-detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(340px, 380px);
        gap: 20px;
        align-items: start;
    }

    .order-detail-main,
    .order-detail-sidebar {
        min-width: 0;
    }

    .order-detail-sidebar {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .order-detail-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
    }

    .order-detail-side-card {
        padding: 20px;
    }

    .order-detail-actions {
        display: flex;
        gap: 8px;
    }

    .order-detail-actions select {
        min-width: 0;
        width: 100%;
    }

    .order-detail-address {
        overflow-wrap: anywhere;
        line-height: 1.5;
    }

    .order-tracking-panel {
        background: #f9fafb;
        border-radius: 10px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(280px, 360px);
        gap: 18px;
        padding: 16px;
    }

    .order-tracking-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 14px;
    }

    .order-tracking-pill {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 999px;
        color: #4b5563;
        font-size: 12px;
        padding: 5px 10px;
    }

    .order-tracking-timeline {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .order-tracking-event {
        display: grid;
        grid-template-columns: 92px 22px minmax(0, 1fr);
        min-height: 54px;
    }

    .order-tracking-time {
        color: #9ca3af;
        font-size: 11px;
        line-height: 1.35;
        padding-top: 1px;
        text-align: right;
        white-space: pre-line;
    }

    .order-tracking-line {
        display: flex;
        justify-content: center;
        position: relative;
    }

    .order-tracking-line::before {
        background: #d1d5db;
        bottom: -1px;
        content: "";
        position: absolute;
        top: 12px;
        width: 1px;
    }

    .order-tracking-event:last-child .order-tracking-line::before {
        display: none;
    }

    .order-tracking-dot {
        background: #d1d5db;
        border: 3px solid #f9fafb;
        border-radius: 999px;
        height: 13px;
        margin-top: 1px;
        width: 13px;
        z-index: 1;
    }

    .order-tracking-event:first-child .order-tracking-dot {
        background: #3b82f6;
    }

    .order-tracking-copy {
        padding: 0 0 16px 6px;
    }

    .order-tracking-status {
        color: #374151;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.3;
    }

    .order-tracking-description {
        color: #6b7280;
        font-size: 12px;
        line-height: 1.4;
        margin-top: 2px;
    }

    .order-tracking-form {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 14px;
    }

    .order-tracking-form-grid {
        display: grid;
        gap: 10px;
    }

    .order-tracking-form-grid input,
    .order-tracking-form-grid select {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        color: #374151;
        font-size: 13px;
        min-width: 0;
        padding: 8px 10px;
        width: 100%;
    }

    .order-tracking-form-grid button {
        background-color: #facc15 !important;
        border-radius: 8px !important;
        color: #ffffff !important;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        min-width: 0 !important;
        padding: 8px 12px !important;
        white-space: nowrap;
    }

    .order-tracking-form-grid button:hover {
        background-color: #eab308 !important;
    }

    .order-tracking-empty {
        color: #9ca3af;
        font-size: 12px;
        padding: 6px 0;
    }

    @media (max-width: 1100px) {
        .order-detail-grid {
            grid-template-columns: 1fr;
        }

        .order-tracking-panel {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .order-detail-page {
            padding: 12px;
        }

        .order-detail-header,
        .order-detail-actions {
            align-items: stretch;
            flex-direction: column;
        }

        .order-tracking-event {
            grid-template-columns: 72px 22px minmax(0, 1fr);
        }
    }
</style>
@stop
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

        <template x-if="!loading && !order">
            <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                <p class="text-sm">Order not found or failed to load.</p>
                <a href="{{ route('admin-order-list') }}" class="mt-3 text-sm text-blue-500 hover:underline">Back to Orders</a>
            </div>
        </template>

        <template x-if="!loading && order">
            <div class="order-detail-page">

                {{-- Header --}}
                <div class="order-detail-header">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-lg font-bold text-gray-800" x-text="`Order #${order.order_no}`"></span>
                            <span class="text-xs px-2 py-0.5 rounded font-semibold bg-gray-100 text-gray-600 uppercase" x-text="order.status"></span>
                            <span class="text-xs px-2 py-0.5 rounded font-semibold uppercase"
                                :class="{
                                    'bg-green-100 text-green-700': order.payment_status === 'paid',
                                    'bg-red-100 text-red-600': order.payment_status === 'unpaid' || order.payment_status === 'failed',
                                    'bg-yellow-100 text-yellow-700': order.payment_status === 'pending',
                                }"
                                x-text="order.payment_status">
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1" x-text="new Date(order.created_at).toLocaleString()"></p>
                    </div>
                    <a href="{{ route('admin-order-list') }}"
                        class="text-sm px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">
                        Back to Orders
                    </a>
                </div>

                {{-- Two column grid --}}
                <div class="order-detail-grid">

                    {{-- LEFT --}}
                    <div class="order-detail-main">

                        {{-- Order Items --}}
                        <div class="order-detail-card mb-5">
                            <div class="px-5 py-3 border-b border-gray-100">
                                <span class="text-sm font-bold text-gray-700">Order Details</span>
                            </div>
                            <div class="px-5 pt-3">
                                <table style="width:100%; border-collapse:collapse;">
                                    <thead>
                                        <tr style="border-bottom:1px solid #f3f4f6;">
                                            <th class="text-left text-xs text-gray-400 font-medium pb-3">Product</th>
                                            <th class="text-right text-xs text-gray-400 font-medium pb-3">Price</th>
                                            <th class="text-right text-xs text-gray-400 font-medium pb-3">Qty</th>
                                            <th class="text-right text-xs text-gray-400 font-medium pb-3">Total</th>
                                        </tr>
                                    </thead>
                                    <template x-for="item in order.items" :key="item.id">
                                        <tbody>
                                            <tr style="border-bottom:1px solid #f9fafb;">
                                                <td class="py-4">
                                                    <div class="flex items-center gap-3">
                                                        <img :src="item.product?.images?.[0]?.url ?? ''"
                                                            x-show="item.product?.images?.[0]?.url"
                                                            class="w-14 h-14 rounded-lg object-cover border border-gray-100">
                                                        <div>
                                                            <p class="text-sm font-semibold text-gray-800" x-text="item.product_name"></p>
                                                            <p class="text-xs text-gray-400" x-show="item.product_sku" x-text="`SKU: ${item.product_sku}`"></p>
                                                            <p class="text-xs text-gray-400" x-show="item.variation_name" x-text="`Variation: ${item.variation_name}`"></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-4 text-right text-sm text-gray-600" x-text="`$${Number(item.unit_price).toFixed(2)}`"></td>
                                                <td class="py-4 text-right text-sm text-gray-600" x-text="item.quantity"></td>
                                                <td class="py-4 text-right text-sm font-bold text-gray-800" x-text="`$${Number(item.line_total).toFixed(2)}`"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="pb-5">
                                                    <div class="order-tracking-panel">
                                                        <div>
                                                            <div class="order-tracking-meta">
                                                                <span class="order-tracking-pill" x-show="item.shipping_carrier" x-text="`Carrier: ${item.shipping_carrier}`"></span>
                                                                <span class="order-tracking-pill" x-show="item.tracking_number" x-text="`Tracking #: ${item.tracking_number}`"></span>
                                                                <span class="order-tracking-pill" x-show="!item.shipping_carrier && !item.tracking_number">No tracking info yet</span>
                                                            </div>

                                                            <template x-if="trackingEvents(item).length === 0">
                                                                <p class="order-tracking-empty">No tracking events have been added for this product.</p>
                                                            </template>

                                                            <div class="order-tracking-timeline" x-show="trackingEvents(item).length">
                                                                <template x-for="event in trackingEvents(item)" :key="`${item.id}-${event.tracked_at}-${event.status}`">
                                                                    <div class="order-tracking-event">
                                                                        <div class="order-tracking-time" x-text="formatTrackingTime(event.tracked_at)"></div>
                                                                        <div class="order-tracking-line">
                                                                            <span class="order-tracking-dot"></span>
                                                                        </div>
                                                                        <div class="order-tracking-copy">
                                                                            <p class="order-tracking-status" x-text="trackingStatusLabel(event.status)"></p>
                                                                            <p class="order-tracking-description" x-text="event.description"></p>
                                                                            <p class="order-tracking-description" x-show="event.location" x-text="event.location"></p>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>

                                                        <div class="order-tracking-form">
                                                            <p class="text-sm font-bold text-gray-700 mb-3">Update Product Tracking</p>
                                                            <div class="order-tracking-form-grid">
                                                                <input type="text" placeholder="Shipping carrier" x-model="trackingForms[item.id].shipping_carrier">
                                                                <input type="text" placeholder="Tracking number" x-model="trackingForms[item.id].tracking_number">
                                                                <select x-model="trackingForms[item.id].status">
                                                                    <option value="">Add timeline status...</option>
                                                                    <option value="created">Created</option>
                                                                    <option value="picked_up">Picked Up</option>
                                                                    <option value="in_transit">In Transit</option>
                                                                    <option value="out_for_delivery">Out for Delivery</option>
                                                                    <option value="delivered">Delivered</option>
                                                                    <option value="failed">Delivery Failed</option>
                                                                    <option value="returned">Returned</option>
                                                                </select>
                                                                <input type="text" placeholder="Tracking message" x-model="trackingForms[item.id].description">
                                                                <input type="text" placeholder="Location" x-model="trackingForms[item.id].location">
                                                                <input type="datetime-local" x-model="trackingForms[item.id].tracked_at">
                                                                <button @click="onUpdateItemTracking(item)"
                                                                    class="bg-yellow-400 hover:bg-yellow-500 text-white text-xs font-semibold px-3 py-2 rounded-lg whitespace-nowrap">
                                                                    Save Tracking
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </template>
                                </table>
                            </div>

                            {{-- Totals --}}
                            <div class="px-5 py-4">
                                <div style="max-width:260px; margin-left:auto;" class="space-y-2 text-sm">
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
                                    <div class="flex justify-between font-bold text-gray-900 pt-2" style="border-top:1px solid #e5e7eb;">
                                        <span>Total:</span>
                                        <span x-text="`$${Number(order.grand_total ?? 0).toFixed(2)}`"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT --}}
                    <div class="order-detail-sidebar">

                        {{-- Customer --}}
                        <div class="order-detail-card order-detail-side-card">
                            <p class="text-sm font-bold text-gray-700 mb-4">Customer Details</p>

                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-pink-400 flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                    x-text="order.user?.name ? order.user.name.split(' ').map(n=>n[0]).join('').slice(0,2).toUpperCase() : '?'">
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800" x-text="order.user?.name ?? '-'"></p>
                                    <p class="text-xs text-gray-400" x-text="`Customer ID: #${order.user?.id ?? '-'}`"></p>
                                </div>
                            </div>

                            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-2">Contact Info</p>
                            <p class="text-xs text-gray-500 mb-1" x-show="order.user?.email" x-text="`Email: ${order.user?.email}`"></p>
                            <p class="text-xs text-gray-500 mb-5" x-text="`Mobile: ${order.user?.phone ?? '-'}`"></p>

                            {{-- Order Status --}}
                            <p class="text-xs text-gray-400 mb-1">Order Status</p>
                            <div class="order-detail-actions mb-4">
                                <select x-model="newStatus" class="flex-1 text-sm border border-gray-200 rounded-lg px-2 py-2">
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="shipping">Shipping</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <button @click="onUpdateStatus()"
                                    class="bg-yellow-400 hover:bg-yellow-500 text-white text-xs font-semibold px-3 py-2 rounded-lg whitespace-nowrap">
                                    Update
                                </button>
                            </div>

                            {{-- Payment Status --}}
                            <p class="text-xs text-gray-400 mb-1">Payment Status</p>
                            <div class="order-detail-actions">
                                <select x-model="newPaymentStatus" class="flex-1 text-sm border border-gray-200 rounded-lg px-2 py-2">
                                    <option value="unpaid">Unpaid</option>
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="failed">Failed</option>
                                    <option value="refunded">Refunded</option>
                                </select>
                                <button @click="onUpdatePaymentStatus()"
                                    class="border border-gray-200 text-gray-600 text-xs px-3 py-2 rounded-lg hover:bg-gray-50 whitespace-nowrap">
                                    Apply
                                </button>
                            </div>
                        </div>

                        {{-- Shipping Address --}}
                        <div class="order-detail-card order-detail-side-card">
                            <p class="text-sm font-bold text-gray-700 mb-3">Shipping Address</p>
                            <p class="text-sm font-semibold text-gray-800" x-text="order.recipient_name ?? '-'"></p>
                            <p class="order-detail-address text-xs text-gray-500 mt-1" x-text="order.shipping_address ?? '-'"></p>
                            <p class="text-xs text-gray-500 mt-1" x-show="order.recipient_phone" x-text="order.recipient_phone"></p>
                            <p class="text-xs text-gray-400 mt-2" x-show="order.shipping_method_title" x-text="`via ${order.shipping_method_title}`"></p>
                        </div>

                        {{-- Note --}}
                        <template x-if="order.note">
                            <div class="order-detail-card order-detail-side-card">
                                <p class="text-sm font-bold text-gray-700 mb-2">Note</p>
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
        trackingForms: {},

        init() {
            const id = new URLSearchParams(location.search).get('id');
            if (!id) { this.loading = false; return; }

            Axios.get("{{ route('admin-order-detail') }}", { params: { id } })
                .then(res => {
                    this.order = res.data?.data ?? null;
                    if (this.order) {
                        this.newStatus        = this.order.status ?? 'pending';
                        this.newPaymentStatus = this.order.payment_status ?? 'unpaid';
                        this.prepareTrackingForms();
                    }
                    this.$nextTick(() => feather.replace());
                })
                .catch(() => {
                    toastr.error('Failed to load order.', { progressBar: true, timeOut: 3000 });
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
                    message: `Update payment status to "${this.newPaymentStatus}"?`,
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

        prepareTrackingForms() {
            this.trackingForms = {};
            (this.order?.items ?? []).forEach(item => {
                this.trackingForms[item.id] = {
                    shipping_carrier: item.shipping_carrier ?? '',
                    tracking_number: item.tracking_number ?? '',
                    status: '',
                    description: '',
                    location: '',
                    tracked_at: this.defaultTrackingDateTime(),
                };
            });
        },

        defaultTrackingDateTime() {
            const date = new Date();
            date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
            return date.toISOString().slice(0, 16);
        },

        trackingEvents(item) {
            return Array.isArray(item.tracking_events) ? item.tracking_events : [];
        },

        trackingStatusLabel(status) {
            return {
                created: 'Created',
                picked_up: 'Picked Up',
                in_transit: 'In Transit',
                out_for_delivery: 'Out for Delivery',
                delivered: 'Delivered',
                failed: 'Delivery Failed',
                returned: 'Returned',
            }[status] ?? (status || '-');
        },

        formatTrackingTime(value) {
            if (!value) return '-';
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return value;

            const dateText = date.toLocaleDateString();
            const timeText = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            return `${dateText}\n${timeText}`;
        },

        onUpdateItemTracking(item) {
            const form = this.trackingForms[item.id];
            if (!form) return;

            Axios.post("{{ route('admin-order-item-tracking') }}", {
                order_id: this.order.id,
                order_item_id: item.id,
                shipping_carrier: form.shipping_carrier,
                tracking_number: form.tracking_number,
                status: form.status,
                description: form.description,
                location: form.location,
                tracked_at: form.tracked_at,
            }).then(res => {
                if (res.data.error === false) {
                    const index = this.order.items.findIndex(row => row.id === item.id);
                    if (index >= 0) {
                        this.order.items[index] = res.data.data;
                    }

                    this.trackingForms[item.id] = {
                        shipping_carrier: res.data.data.shipping_carrier ?? '',
                        tracking_number: res.data.data.tracking_number ?? '',
                        status: '',
                        description: '',
                        location: '',
                        tracked_at: this.defaultTrackingDateTime(),
                    };
                    toastr.success(res.data.message, { progressBar: true, timeOut: 3000 });
                } else {
                    toastr.error(res.data.message ?? 'Something went wrong!', { progressBar: true, timeOut: 3000 });
                }
            }).catch(e => {
                toastr.error(e?.response?.data?.message ?? 'Something went wrong!', { progressBar: true, timeOut: 3000 });
            });
        },
    }));
</script>
@stop
