@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="orderPage">
        @include('admin::shared.header', [
            'title' => 'Orders',
            'header_name' => 'Orders',
        ])
        <div class="content-body">
            <div class="content-tab">
                <div class="content-tab-wrapper">
                    <span class="title !text-gray-600">
                        @lang('form.total') <span x-text="table?.paginate?.totalItems"></span>
                    </span>
                </div>
                <div class="content-action-button">
                    <div class="filter">
                        <div class="form-row search-inline">
                            <input type="text" x-model="formFilter.search" placeholder="Search order..."
                                autocomplete="off" @keydown.enter="onFilter()">
                            <button @click="onFilter()"><i data-feather="search"></i></button>
                        </div>
                    </div>
                    <button @click="onReset()">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
            @include('admin::pages.order.table')
        </div>
    </div>
@stop
@section('script')
    <script type="module">
        Alpine.data('orderPage', () => ({
            table: new Table("{{ route('admin-order-data') }}"),
            formFilter: new FormGroup({
                search: ['', []],
                status: ['', []],
            }),
            init() {
                this.table.init();
                feather.replace();
            },
            onFilter() {
                this.table.init(this.formFilter.value());
            },
            onReset() {
                this.formFilter.reset();
                this.table.reset();
            },
            onUpdateStatus(id, status) {
                this.$store.confirmDialog.open({
                    data: {
                        title: "@lang('dialog.title')",
                        message: `Update order status to ${status}?`,
                        btnClose: "@lang('dialog.button.close')",
                        btnSave: "@lang('dialog.button.save')",
                    },
                    afterClosed: (result) => {
                        if (!result) return;
                        Axios.post("{{ route('admin-order-status') }}", { id, status })
                            .then((res) => {
                                if (res.data.error === false) {
                                    toastr.success(res.data.message, { progressBar: true, timeOut: 5000 });
                                    this.table.reload();
                                } else {
                                    toastr.error(res.data.message ?? 'Something went wrong!', { progressBar: true, timeOut: 5000 });
                                }
                            }).catch((e) => {
                                toastr.error(e?.response?.data?.message ?? 'Something went wrong!', { progressBar: true, timeOut: 5000 });
                            });
                    }
                });
            },
        }));
    </script>
@stop

