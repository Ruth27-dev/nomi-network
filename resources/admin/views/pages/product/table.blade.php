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
                            <div class="flex h-11 px-4">
                                <div class="w-5/100 text-xs font-semibold text-gray-400 uppercase tracking-wide grid place-items-center">
                                    <span>@lang('table.field.no')</span>
                                </div>
                                <div class="w-25/100 text-xs font-semibold text-gray-400 uppercase tracking-wide flex items-center">
                                    <span>@lang('table.field.title')</span>
                                </div>
                                <div class="w-15/100 text-xs font-semibold text-gray-400 uppercase tracking-wide flex items-center">
                                    <span>@lang('table.field.category')</span>
                                </div>
                                <div class="w-10/100 text-xs font-semibold text-gray-400 uppercase tracking-wide grid place-items-center">
                                    <span>@lang('table.field.price')</span>
                                </div>
                                <div class="w-10/100 text-xs font-semibold text-gray-400 uppercase tracking-wide grid place-items-center">
                                    <span>@lang('table.field.quantity')</span>
                                </div>
                                <div class="w-15/100 text-xs font-semibold text-gray-400 uppercase tracking-wide grid place-items-center">
                                    <span>Type</span>
                                </div>
                                <div class="w-15/100 text-xs font-semibold text-gray-400 uppercase tracking-wide grid place-items-center">
                                    <span>@lang('table.field.status')</span>
                                </div>
                                <div class="w-5/100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-body w-full! p-0!">
                <template x-for="(item, index) in table.data">
                    <div class="w-full flex gap-3 h-[70px]">
                        <div class="flex-auto border-b border-gray-100">
                            <div class="flex row-item h-full px-4 hover:bg-gray-50 cursor-pointer">

                                {{-- No --}}
                                <div class="w-5/100 grid place-items-center">
                                    <span class="text-sm text-gray-400" x-text="index + 1"></span>
                                </div>

                                {{-- Product: thumbnail + name + sku --}}
                                <div class="w-25/100 flex items-center gap-3 pr-4">
                                    <template x-if="item.images && item.images.length > 0">
                                        <img :src="item.images[0].url"
                                            style="width:40px;height:40px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;flex-shrink:0;cursor:pointer;"
                                            @click.stop="onViewProfile(item.images[0].url)" />
                                    </template>
                                    <template x-if="!item.images || item.images.length === 0">
                                        <div style="width:40px;height:40px;flex-shrink:0;border-radius:6px;border:1px solid #e5e7eb;background:#f9fafb;display:grid;place-items:center;">
                                            <span class="material-icons" style="font-size:18px;color:#d1d5db;">image</span>
                                        </div>
                                    </template>
                                    <div class="flex flex-col min-w-0">
                                        <span class="text-sm font-medium text-gray-800 truncate"
                                            x-text="item.title ? (item.title[langLocale] ?? item.title['en'] ?? '-') : '-'"></span>
                                        <span class="text-xs text-gray-400"
                                            x-text="item.code ?? ''"></span>
                                    </div>
                                </div>

                                {{-- Category --}}
                                <div class="w-15/100 flex items-center pr-4">
                                    <span class="text-sm text-gray-500 truncate"
                                        x-text="item.category ? (item.category.title?.[langLocale] ?? item.category.title?.['en'] ?? '-') : '-'"></span>
                                </div>

                                {{-- Price --}}
                                <div class="w-10/100 grid place-items-center">
                                    <span class="text-sm font-medium text-gray-700"
                                        x-text="'$' + Number(item.price ?? 0).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})"></span>
                                </div>

                                {{-- Stock --}}
                                <div class="w-10/100 grid place-items-center">
                                    <template x-if="(item.stock ?? 0) > 0">
                                        <span class="text-sm text-gray-700"
                                            x-text="Number(item.stock).toLocaleString()"></span>
                                    </template>
                                    <template x-if="(item.stock ?? 0) <= 0">
                                        <span style="background:#fef2f2;color:#ef4444;border-radius:999px;padding:2px 10px;font-size:11px;font-weight:600;white-space:nowrap;">Out of stock</span>
                                    </template>
                                </div>

                                {{-- Type badges --}}
                                <div class="w-15/100 flex items-center justify-center gap-2">
                                    <template x-if="item.has_variation">
                                        <span style="background:#f5f3ff;color:#7c3aed;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;white-space:nowrap;">Variation</span>
                                    </template>
                                    <template x-if="item.is_preorder">
                                        <span style="background:#fffbeb;color:#d97706;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;white-space:nowrap;">Pre-order</span>
                                    </template>
                                </div>

                                {{-- Status --}}
                                <div class="w-15/100 grid place-items-center">
                                    <template x-if="item.status == `{{ config('dummy.status.active.key') }}`">
                                        <span style="display:inline-flex;align-items:center;gap:6px;background:#f0fdf4;color:#16a34a;border-radius:999px;padding:4px 12px;font-size:12px;font-weight:600;white-space:nowrap;">
                                            <span style="width:6px;height:6px;border-radius:50%;background:#16a34a;flex-shrink:0;display:inline-block;"></span>{{ config('dummy.status.active.text') }}
                                        </span>
                                    </template>
                                    <template x-if="item.status == `{{ config('dummy.status.inactive.key') }}`">
                                        <span style="display:inline-flex;align-items:center;gap:6px;background:#f9fafb;color:#6b7280;border-radius:999px;padding:4px 12px;font-size:12px;font-weight:600;white-space:nowrap;">
                                            <span style="width:6px;height:6px;border-radius:50%;background:#9ca3af;flex-shrink:0;display:inline-block;"></span>{{ config('dummy.status.inactive.text') }}
                                        </span>
                                    </template>
                                </div>

                                {{-- Actions --}}
                                <div class="w-5/100 text-gray-300 grid place-items-center">
                                    <div x-data="{
                                        open: false,
                                        toggle() {
                                            if (this.open) { return this.close() }
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
                                        @canany(['product-update', 'product-delete', 'product-restore'])
                                            <div x-ref="button" x-on:click="toggle()" :aria-expanded="open"
                                                :aria-controls="$id('dropdown-button')" type="button" class="action-btn">
                                                <i data-feather="more-vertical" class="cursor-pointer"></i>
                                            </div>
                                        @endcanany
                                        <ul x-ref="panel" x-show="open" x-transition.origin.top.right
                                            x-on:click.outside="close($refs.button)" :id="$id('dropdown-button')"
                                            style="display: none;" class="absolute right-0 dropdown-menu">
                                            @can('product-create')
                                                <li x-show="!item.deleted_at">
                                                    <a class="dropdown-item" @click="openStoreCopyItemDialog(item?.id)">
                                                        <span class="material-icons text-violet-600 cursor-pointer">content_copy</span>
                                                        <span class="text-sm text-gray-600 ml-2">@lang('table.option.copy')</span>
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('item-update')
                                                <li x-show="!item.deleted_at">
                                                    <a class="dropdown-item" @click="openStoreItemDialog(item?.id)">
                                                        <span class="material-icons text-violet-600 cursor-pointer">edit</span>
                                                        <span class="text-sm text-gray-600 ml-2">@lang('table.option.edit')</span>
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('product-update')
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
                                            @can('product-delete')
                                                <template x-if="!item.deleted_at">
                                                    <li>
                                                        <a class="dropdown-item" @click="onDelete(item?.id)">
                                                            <span class="material-icons text-red-500 cursor-pointer">delete</span>
                                                            <span class="text-sm text-gray-600 ml-2">@lang('table.option.delete')</span>
                                                        </a>
                                                    </li>
                                                </template>
                                            @endcan
                                            @can('product-restore')
                                                <template x-if="item.deleted_at">
                                                    <li>
                                                        <a class="dropdown-item" @click="onRestore(item?.id)">
                                                            <span class="material-icons text-green-500 cursor-pointer">replay</span>
                                                            <span class="text-sm text-gray-600 ml-2">@lang('table.option.restore')</span>
                                                        </a>
                                                    </li>
                                                </template>
                                            @endcan
                                            <li>
                                                <a class="dropdown-item" @click="openViewVariationDialog(item?.id)">
                                                    <span class="material-icons text-blue-500 cursor-pointer">visibility</span>
                                                    <span class="text-sm text-gray-600 ml-2">@lang('table.option.view_variation')</span>
                                                </a>
                                            </li>
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
