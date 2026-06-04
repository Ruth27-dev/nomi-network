<template x-dialog="adjustStockDialog">
    <div x-data="adjustStockDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 class="text-gray-600">Adjust Stock</h3>
                <span @click="$dialog('adjustStockDialog').close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto">
                <div class="rounded-lg border border-blue-100 bg-blue-50 px-3 py-2 mb-3 text-xs text-blue-700 leading-5">
                    Enter the quantity change, not the final stock number. Use a positive number to add stock and a negative number to remove stock.
                </div>

                <div class="form-row">
                    <label>Product</label>
                    <input type="text" x-model="form.product_name" disabled>
                </div>

                {{-- Current stock overview --}}
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 mb-1">
                    <div class="text-[11px] text-gray-400 uppercase tracking-wide font-medium mb-2">Current Stock</div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="text-center">
                            <div class="text-[11px] text-gray-400 mb-0.5">On Hand</div>
                            <div class="text-lg font-bold text-gray-700" x-text="form.stock_on_hand"></div>
                        </div>
                        <div class="text-center">
                            <div class="text-[11px] text-gray-400 mb-0.5">Reserved</div>
                            <div class="text-lg font-bold text-gray-500" x-text="form.stock_reserved"></div>
                        </div>
                        <div class="text-center">
                            <div class="text-[11px] text-gray-400 mb-0.5">Available</div>
                            <div class="text-lg font-bold"
                                :class="(form.stock_available ?? 0) <= 0 ? 'text-red-500' : ((form.stock_available ?? 0) <= 5 ? 'text-amber-500' : 'text-emerald-600')"
                                x-text="form.stock_available"></div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <label>Quantity Change<span>*</span></label>
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <button type="button"
                            class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700"
                            @click="setAdjustmentMode('add')"
                            :disabled="loading">
                            + Add Stock
                        </button>
                        <button type="button"
                            class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700"
                            @click="setAdjustmentMode('remove')"
                            :disabled="loading">
                            - Remove Stock
                        </button>
                    </div>
                    <input type="number" x-model="form.adjust_qty" placeholder="Example: 10 to add, -5 to remove" :disabled="loading">
                    <div class="text-xs text-gray-400 mt-1">
                        Current on hand will change from
                        <span class="font-semibold text-gray-600" x-text="form.stock_on_hand"></span>
                        to
                        <span class="font-semibold"
                            :class="newOnHand() < 0 ? 'text-red-600' : 'text-gray-700'"
                            x-text="newOnHand()"></span>.
                    </div>
                    <span class="error" x-show="validate?.adjust_qty" x-text="validate?.adjust_qty"></span>
                </div>

                {{-- Live preview --}}
                <template x-if="form.adjust_qty !== '' && Number(form.adjust_qty) !== 0">
                    <div class="rounded-lg border px-3 py-3 mt-1"
                        :class="(Number(form.stock_on_hand) + Number(form.adjust_qty || 0)) < 0
                            ? 'border-red-200 bg-red-50'
                            : 'border-blue-100 bg-blue-50'">
                        <div class="text-[11px] font-medium uppercase tracking-wide mb-2"
                            :class="(Number(form.stock_on_hand) + Number(form.adjust_qty || 0)) < 0 ? 'text-red-400' : 'text-blue-400'">
                            Preview After Save
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <div class="text-[11px] text-gray-400 mb-0.5">On Hand</div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-sm text-gray-400" x-text="form.stock_on_hand"></span>
                                    <i data-feather="arrow-right" class="w-3 h-3 text-gray-400"></i>
                                    <span class="text-sm font-bold"
                                        :class="newOnHand() < 0 ? 'text-red-600' : 'text-gray-700'"
                                        x-text="newOnHand()"></span>
                                </div>
                            </div>
                            <div>
                                <div class="text-[11px] text-gray-400 mb-0.5">Available</div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-sm text-gray-400" x-text="form.stock_available"></span>
                                    <i data-feather="arrow-right" class="w-3 h-3 text-gray-400"></i>
                                    <span class="text-sm font-bold text-gray-700"
                                        x-text="newAvailable()"></span>
                                </div>
                            </div>
                        </div>
                        <template x-if="(Number(form.stock_on_hand) + Number(form.adjust_qty || 0)) < 0">
                            <div class="mt-2 text-xs text-red-500 flex items-center gap-1">
                                <i data-feather="alert-circle" class="w-3 h-3"></i>
                                Stock on hand cannot go below zero.
                            </div>
                        </template>
                    </div>
                </template>
            </div>
            <div class="form-footer sticky bottom-0 bg-white z-10">
                <div class="form-button">
                    <button type="button" color="primary" @click="onSave()" :disabled="loading">
                        <span class="material-icons mr-1">save</span>
                        <span>Save</span>
                        <div class="loader" style="display: none" x-show="loading"></div>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        Alpine.data('adjustStockDialog', () => ({
            loading: false,
            validate: null,
            stockId: null,
            form: new FormGroup({
                product_name: ['', []],
                stock_on_hand: [0, []],
                stock_reserved: [0, []],
                stock_available: [0, []],
                adjust_qty: ['', ['required']],
            }),
            init() {
                const stock = this.$dialog('adjustStockDialog').data?.stock;
                this.stockId = stock?.id;
                this.form.product_name = stock?.product?.name_en ?? '-';
                this.form.stock_on_hand = stock?.stock_on_hand ?? 0;
                this.form.stock_reserved = stock?.stock_reserved ?? 0;
                this.form.stock_available = stock?.stock_available ?? 0;
                feather.replace();
            },
            newOnHand() {
                return Number(this.form.stock_on_hand) + Number(this.form.adjust_qty || 0);
            },
            newAvailable() {
                return Math.max(0, this.newOnHand() - Number(this.form.stock_reserved));
            },
            setAdjustmentMode(mode) {
                const currentValue = Math.abs(Number(this.form.adjust_qty || 1));
                this.form.adjust_qty = mode === 'remove' ? -currentValue : currentValue;
            },
            onSave() {
                this.validate = null;
                this.loading = true;
                Axios.post("{{ route('admin-product-stock-adjust') }}", {
                    id: this.stockId,
                    adjust_qty: Number(this.form.adjust_qty),
                }).then((res) => {
                    if (res.data.error === false) {
                        toastr.success(res.data.message, { progressBar: true, timeOut: 5000 });
                        this.$dialog('adjustStockDialog').close(true);
                    } else {
                        toastr.error(res.data.message ?? 'Something went wrong!', { progressBar: true, timeOut: 5000 });
                    }
                }).catch((e) => {
                    this.validate = e?.response?.data?.errors ?? null;
                    toastr.error(e?.response?.data?.message ?? 'Something went wrong!', { progressBar: true, timeOut: 5000 });
                }).finally(() => {
                    this.loading = false;
                });
            },
        }));
    </script>
</template>
