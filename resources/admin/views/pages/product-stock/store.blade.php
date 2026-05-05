<template x-dialog="adjustStockDialog">
    <div x-data="adjustStockDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 class="text-gray-600">Adjust Stock</h3>
                <span @click="$dialog('adjustStockDialog').close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto">
                <div class="form-row">
                    <label>Product</label>
                    <input type="text" x-model="form.product_name" disabled>
                </div>
                <div class="row-3">
                    <div class="form-row">
                        <label>On Hand</label>
                        <input type="number" x-model="form.stock_on_hand" disabled>
                    </div>
                    <div class="form-row">
                        <label>Reserved</label>
                        <input type="number" x-model="form.stock_reserved" disabled>
                    </div>
                    <div class="form-row">
                        <label>Available</label>
                        <input type="number" x-model="form.stock_available" disabled>
                    </div>
                </div>
                <div class="form-row">
                    <label>Adjust Qty<span>*</span></label>
                    <input type="number" x-model="form.adjust_qty" placeholder="Use + or - number (e.g. 10, -5)" :disabled="loading">
                    <span class="error" x-show="validate?.adjust_qty" x-text="validate?.adjust_qty"></span>
                </div>
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

