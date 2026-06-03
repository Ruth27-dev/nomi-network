@extends('admin::shared.layout')
@section('layout')
    <div class="content-wrapper" x-data="donationPage">
        @include('admin::shared.header', [
            'title' => 'Donations',
            'header_name' => 'Donations',
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
                        <select x-model="formFilter.payment_status" @change="onFilter()" class="text-xs border border-gray-300 rounded px-2 py-1">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                        </select>
                        <select x-model="formFilter.donation_type" @change="onFilter()" class="text-xs border border-gray-300 rounded px-2 py-1">
                            <option value="">All Types</option>
                            <option value="one_time">One Time</option>
                            <option value="monthly">Monthly</option>
                        </select>
                        <div class="form-row search-inline">
                            <input type="text" x-model="formFilter.search" placeholder="Search name, tran id..."
                                autocomplete="off" @keydown.enter="onFilter()">
                            <button @click="onFilter()"><i data-feather="search"></i></button>
                        </div>
                    </div>
                    <button @click="onReset()">
                        <i data-feather="refresh-ccw"></i>
                    </button>
                </div>
            </div>
            @include('admin::pages.donation.table')
        </div>
    </div>
@stop
@section('script')
    <script type="module">
        Alpine.data('donationPage', () => ({
            table: new Table("{{ route('admin-donation-data') }}"),
            formFilter: new FormGroup({
                search:         ['', []],
                payment_status: ['', []],
                donation_type:  ['', []],
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
        }));
    </script>
@stop
