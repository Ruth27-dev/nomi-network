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
                                <div class="w-25/100 text-sm font-bold text-gray-500 flex items-center">
                                    <span>Product</span>
                                </div>
                                <div class="w-25/100 text-sm font-bold text-gray-500 flex items-center">
                                    <span>@lang('table.field.title')</span>
                                </div>
                                <div class="w-15/100 text-sm font-bold text-gray-500 grid place-items-center">
                                    <span>@lang('table.field.price')</span>
                                </div>
                                <div class="w-20/100 text-sm font-bold text-gray-500 grid place-items-center">
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
                                <div class="w-25/100 text-gray-500 flex items-center">
                                    <span class="text-sm text-center"
                                        x-text="item.product ? item.product.title?.[langLocale] : '-'"></span>
                                </div>
                                <div class="w-25/100 text-gray-500 flex items-center">
                                    <span class="text-sm text-center"
                                        x-text="item.title ? item.title?.[langLocale] : '-'"></span>
                                </div>
                                <div class="w-15/100 text-gray-500 grid place-items-center">
                                    <span class="text-sm" x-text="item?.price ?? '-'"></span>
                                </div>
                                <div class="w-20/100 text-gray-500 grid place-items-center">
                                    <template x-if="item.status == `{{ config('dummy.status.active.key') }}`">
                                        <span
                                            class="capitalize inline-block whitespace-nowrap rounded-full bg-green-100 px-[0.65em] pt-[0.35em] pb-[0.25em] text-center align-baseline text-[12px] font-bold leading-none text-green-600">
                                            {{ config('dummy.status.active.text') }}
                                        </span>
                                    </template>
                                    <template x-if="item.status == `{{ config('dummy.status.inactive.key') }}`">
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
                                        @canany(['product-update', 'product-delete'])
                                            <div x-ref="button" x-on:click="toggle()" :aria-expanded="open"
                                                :aria-controls="$id('dropdown-button')" type="button" class="action-btn">
                                                <i data-feather="more-vertical" class="cursor-pointer"></i>
                                            </div>
                                        @endcanany
                                        <ul x-ref="panel" x-show="open" x-transition.origin.top.right
                                            x-on:click.outside="close($refs.button)" :id="$id('dropdown-button')"
                                            style="display: none;" class="absolute right-0 dropdown-menu">
                                            @can('product-update')
                                                <li>
                                                    <a class="dropdown-item" @click="openStoreVariationDialog(item?.id)">
                                                        <span
                                                            class="material-icons text-violet-600 cursor-pointer">edit</span>
                                                        <span class="text-sm text-gray-600 ml-2">@lang('table.option.edit')</span>
                                                    </a>
                                                </li>
                                                <template x-if="item.status == 'INACTIVE'">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            @click="onUpdateStatus(item?.id, 'ACTIVE')">
                                                            <span
                                                                class="material-icons text-green-500 cursor-pointer">change_circle</span>
                                                            <span
                                                                class="text-sm text-gray-600 ml-2">@lang('table.option.enable')</span>
                                                        </a>
                                                    </li>
                                                </template>
                                                <template x-if="item.status == 'ACTIVE'">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            @click="onUpdateStatus(item?.id, 'INACTIVE')">
                                                            <span
                                                                class="material-icons text-orange-500 cursor-pointer">close</span>
                                                            <span
                                                                class="text-sm text-gray-600 ml-2">@lang('table.option.disable')</span>
                                                        </a>
                                                    </li>
                                                </template>
                                            @endcan
                                            @can('product-delete')
                                                <li>
                                                    <a class="dropdown-item" @click="onDelete(item?.id)">
                                                        <span
                                                            class="material-icons text-red-500 cursor-pointer">delete</span>
                                                        <span
                                                            class="text-sm text-gray-600 ml-2">@lang('table.option.delete')</span>
                                                    </a>
                                                </li>
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
        @component('admin::components.empty', [
            'name' => __('table.empty.title', ['name' => null]),
            'msg' => __('table.empty.message', ['name' => null]),
        ])
        @endcomponent
    </template>
</div>
