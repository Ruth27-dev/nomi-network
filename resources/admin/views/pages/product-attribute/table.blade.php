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
                                <div class="w-5/100 text-sm font-bold text-gray-500 grid place-items-center">
                                    <span>No</span>
                                </div>
                                <div class="w-25/100 text-sm font-bold text-gray-500 flex items-center">
                                    <span>Name</span>
                                </div>
                                <div class="w-15/100 text-sm font-bold text-gray-500 flex items-center">
                                    <span>Code</span>
                                </div>
                                <div class="w-15/100 text-sm font-bold text-gray-500 flex items-center">
                                    <span>Input Type</span>
                                </div>
                                <div class="w-25/100 text-sm font-bold text-gray-500 flex items-center">
                                    <span>Values</span>
                                </div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 grid place-items-center">
                                    <span>Status</span>
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
                <template x-for="(item, index) in table.data">
                    <div class="w-full flex gap-3 h-[70px]">
                        <div class="flex-auto border-b border-gray-200">
                            <div class="flex row-item h-full hover:bg-type_gray">
                                <div class="w-5/100 grid place-items-center text-gray-500">
                                    <span class="text-sm" x-text="index + 1"></span>
                                </div>
                                <div class="w-25/100 text-gray-600 flex items-center">
                                    <span class="text-sm" x-text="item.name"></span>
                                </div>
                                <div class="w-15/100 text-gray-600 flex items-center">
                                    <span class="text-sm" x-text="item.code ?? '-'"></span>
                                </div>
                                <div class="w-15/100 text-gray-600 flex items-center">
                                    <span class="text-sm" x-text="item.input_type ?? '-'"></span>
                                </div>
                                <div class="w-25/100 text-gray-600 flex items-center pr-2">
                                    <span class="text-sm" x-text="item.values && item.values.length > 0 ? item.values.map(v => v.value).join(', ') : '-'"></span>
                                </div>
                                <div class="w-10/100 text-gray-500 grid place-items-center">
                                    <template x-if="item.status == active">
                                        <span class="inline-block whitespace-nowrap rounded-full bg-green-100 px-[0.65em] pt-[0.35em] pb-[0.25em] text-center align-baseline text-[12px] font-bold leading-none text-green-600">
                                            {{ config('dummy.status.active.text') }}
                                        </span>
                                    </template>
                                    <template x-if="item.status == inactive">
                                        <span class="inline-block whitespace-nowrap rounded-full bg-red-100 px-[0.65em] pt-[0.35em] pb-[0.25em] text-center align-baseline text-[12px] font-bold leading-none text-red-600">
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
                                        @canany(['product-attribute-update', 'product-attribute-delete'])
                                            <div x-ref="button" x-on:click="toggle()" :aria-expanded="open"
                                                :aria-controls="$id('dropdown-button')" type="button" class="action-btn">
                                                <i data-feather="more-vertical" class="cursor-pointer"></i>
                                            </div>
                                        @endcanany
                                        <ul x-ref="panel" x-show="open" x-transition.origin.top.right
                                            x-on:click.outside="close($refs.button)" :id="$id('dropdown-button')"
                                            style="display: none;" class="absolute right-0 dropdown-menu">
                                            @can('product-attribute-update')
                                                <li>
                                                    <a class="dropdown-item" @click="openStoreDialog(item.id)">
                                                        <span class="material-icons text-violet-600 cursor-pointer">edit</span>
                                                        <span class="text-sm text-gray-600 ml-2">Edit</span>
                                                    </a>
                                                </li>
                                                <template x-if="item.status == inactive">
                                                    <li>
                                                        <a class="dropdown-item" @click="onUpdateStatus(item.id, active)">
                                                            <span class="material-icons text-green-500 cursor-pointer">change_circle</span>
                                                            <span class="text-sm text-gray-600 ml-2">Enable</span>
                                                        </a>
                                                    </li>
                                                </template>
                                                <template x-if="item.status == active">
                                                    <li>
                                                        <a class="dropdown-item" @click="onUpdateStatus(item.id, inactive)">
                                                            <span class="material-icons text-orange-500 cursor-pointer">close</span>
                                                            <span class="text-sm text-gray-600 ml-2">Disable</span>
                                                        </a>
                                                    </li>
                                                </template>
                                            @endcan
                                            @can('product-attribute-delete')
                                                <li>
                                                    <a class="dropdown-item" @click="onDelete(item.id)">
                                                        <span class="material-icons text-red-500 cursor-pointer">delete</span>
                                                        <span class="text-sm text-gray-600 ml-2">Delete</span>
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
        @component('admin::components.empty', ['name' => 'No attributes', 'msg' => 'No attributes found'])
        @endcomponent
    </template>
</div>
