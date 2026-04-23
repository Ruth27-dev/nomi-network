<div class="table">
    <template x-if="table?.loading">
        @include('admin::components.progress-bar', ['top' => true]);
    </template>
    <template x-if="!table.loading && !table?.empty()">
        <div class="table-wrapper">
            <div class="table-header">
                <div class="flex flex-col flex-auto">
                    <div class="w-full flex gap-3">
                        <div class="flex-auto border-t-[1px] border-b-[1px] border-gray-200 bg-gray-50">
                            <div class="flex h-11">
                                <div class="w-5/100 text-sm font-bold text-gray-500 grid place-items-center">
                                    <span>@lang('table.field.no')</span>
                                </div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 flex items-center">
                                    <span>@lang('table.field.type')</span>
                                </div>
                                <div class="w-15/100 text-sm font-bold text-gray-500 flex items-center">
                                    <span>@lang('table.field.code')</span>
                                </div>
                                <div class="w-20/100 text-sm font-bold text-gray-500 flex items-center">
                                    <span>@lang('table.field.title')</span>
                                </div>
                                <div class="w-15/100 text-sm font-bold text-gray-500 grid place-items-center">
                                    <span>@lang('table.field.discount_amount')</span>
                                </div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">
                                    <span>@lang('table.field.start_date')</span>
                                </div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">
                                    <span>@lang('table.field.end_date')</span>
                                </div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">
                                    <span>@lang('table.field.status')</span>
                                </div>
                                <div class="w-5/100 text-sm font-bold text-gray-500 grid place-items-center">
                                    <span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-body !w-full !p-0">
                <template x-for="(item,index) in table.data">
                    <div class="w-full flex gap-3 h-[70px]">
                        <div class="flex-auto border-b-[1px] border-gray-200">
                            <div class="flex row-item h-full hover:bg-gray-50 cursor-pointer">
                                <div class="w-5/100 grid place-items-center text-gray-500">
                                    <span class="text-sm" x-text="index + 1"></span>
                                </div>
                                <div class="w-10/100 text-gray-500 flex items-center">
                                    <span class="text-sm" x-text="item?.type ?? '-'"></span>
                                </div>
                                <div class="w-15/100 text-gray-500 flex items-center">
                                    <span class="text-sm" x-text="item?.code ?? '-'"></span>
                                </div>
                                <div class="w-20/100 text-gray-500 flex items-center">
                                    <span class="text-sm" x-text="item?.title?.[langLocale] ?? item?.title?.en ?? '-'"></span>
                                </div>
                                <div class="w-15/100 text-gray-500 grid place-items-center">
                                    <span class="text-sm"
                                        x-text="item?.discount_amount ? parseFloat(item?.discount_amount).toFixed(2) + ' ' + (item?.discount_type == appConfig.discount.type.percentage ? '%' : '$') : '-'"></span>
                                </div>
                                <div class="w-10/100 text-gray-500 grid place-items-center">
                                    <span class="text-sm" x-text="item?.start_date ? dateFormat(item?.start_date) : '-'"></span>
                                </div>
                                <div class="w-10/100 text-gray-500 grid place-items-center">
                                    <span class="text-sm" x-text="item?.end_date ? dateFormat(item?.end_date) : '-'"></span>
                                </div>
                                <div class="w-10/100 text-gray-500 grid place-items-center">
                                    <template x-if="item.status == appConfig.status.active">
                                        <span
                                            class="capitalize inline-block whitespace-nowrap rounded-full bg-green-100 px-[0.65em] pt-[0.35em] pb-[0.25em] text-center align-baseline text-[12px] font-bold leading-none text-green-600">
                                            {{ config('dummy.status.active.text') }}
                                        </span>
                                    </template>
                                    <template x-if="item.status == appConfig.status.inactive">
                                        <span
                                            class="capitalize inline-block whitespace-nowrap rounded-full bg-red-100 px-[0.65em] pt-[0.35em] pb-[0.25em] text-center align-baseline text-[12px] font-bold leading-none text-red-600">
                                            {{ config('dummy.status.inactive.text') }}
                                        </span>
                                    </template>
                                </div>
                                <div class="w-5/100 text-gray-300 grid place-items-center">
                                    <div x-data="{
                                        open: false,
                                        toggle() {
                                            if (this.open) {
                                                return this.close()
                                            }

                                            this.$refs.button.focus()

                                            this.open = true
                                        },
                                        close(focusAfter) {
                                            if (!this.open) return

                                            this.open = false

                                            focusAfter && focusAfter.focus()
                                        }
                                    }" x-on:keydown.escape.prevent.stop="close($refs.button)"
                                        x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
                                        x-id="['dropdown-button']" class="relative dropdown">
                                        @canany(['product-discount-update', 'product-discount-update-status', 'product-discount-delete', 'product-discount-restore'])
                                            <div x-ref="button" x-on:click="toggle()" :aria-expanded="open"
                                                :aria-controls="$id('dropdown-button')" type="button" class="action-btn">
                                                <i data-feather="more-vertical" class="cursor-pointer"></i>
                                            </div>
                                        @endcanany
                                        <ul x-ref="panel" x-show="open" x-transition.origin.top.right
                                            x-on:click.outside="close($refs.button)" :id="$id('dropdown-button')"
                                            style="display: none;" class="absolute right-0 dropdown-menu">

                                            @can('product-discount-update')
                                                <li x-show="!item.deleted_at">
                                                    <a class="dropdown-item" @click="openStoreProductDiscountDialog(item?.id)">
                                                        <span class="material-icons text-violet-600 cursor-pointer">edit</span>
                                                        <span class="text-sm text-gray-600 ml-2">@lang('table.option.edit')</span>
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('product-discount-update-status')
                                                <template x-if="!item.deleted_at && item.status == 'INACTIVE'">
                                                    <li>
                                                        <a class="dropdown-item" @click="onUpdateStatus(item?.id, 'ACTIVE')">
                                                            <span class="material-icons text-green-500 cursor-pointer">change_circle</span>
                                                            <span class="text-sm text-gray-600 ml-2">@lang('table.option.enable')</span>
                                                        </a>
                                                    </li>
                                                </template>
                                                <template x-if="!item.deleted_at && item.status == 'ACTIVE'">
                                                    <li>
                                                        <a class="dropdown-item" @click="onUpdateStatus(item?.id, 'INACTIVE')">
                                                            <span class="material-icons text-orange-500 cursor-pointer">close</span>
                                                            <span class="text-sm text-gray-600 ml-2">@lang('table.option.disable')</span>
                                                        </a>
                                                    </li>
                                                </template>
                                            @endcan
                                            @can('product-discount-delete')
                                                <template x-if="!item.deleted_at">
                                                    <li>
                                                        <a class="dropdown-item" @click="onDelete(item?.id)">
                                                            <span class="material-icons text-red-500 cursor-pointer">delete</span>
                                                            <span class="text-sm text-gray-600 ml-2">@lang('table.option.delete')</span>
                                                        </a>
                                                    </li>
                                                </template>
                                            @endcan
                                        </ul>
                                    </div>
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
        @component('admin::components.empty',
            [
                'name' => __('table.empty.title', ['name' => null]),
                'msg' => __('table.empty.message', ['name' => null]),
            ])
        @endcomponent
    </template>
</div>
