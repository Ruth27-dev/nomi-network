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
                                    <span> @lang('table.field.no')</span>
                                </div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 flex items-center">
                                    <span> @lang('table.field.image')</span>
                                </div>
                                <div class="w-20/100 text-sm font-bold text-gray-500 flex items-center">
                                    <span>@lang('table.field.title')</span>
                                </div>
                                <div class="w-15/100 text-sm font-bold text-gray-500 flex items-center">
                                    <span>@lang('table.field.page')</span>
                                </div>
                                <div class="w-20/100 text-sm font-bold text-gray-500 flex items-center">
                                    <span>@lang('table.field.link')</span>
                                </div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 flex items-center">
                                    <span> @lang('table.field.ordering')</span>
                                </div>
                                <div class="w-10/100 text-sm font-bold text-gray-500 flex items-center">
                                    <span> @lang('table.field.status')</span>
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
                    <div class="w-full flex gap-3 h-[70px]" x-data="{ status: item.status }">
                        <div class="flex-auto border-b-[1px] border-gray-200">
                            <div class="flex row-item h-full hover:bg-type_gray">
                                <div class="w-5/100 grid place-items-center text-gray-500">
                                    <span class="text-sm" x-text="index + 1"></span>
                                </div>
                                <div class="w-10/100 text-gray-500 flex items-center">
                                    <div @click="onViewImage(item.image_url)" class="cursor-pointer h-[50px] w-[50px]">
                                        <img x-bind:src="item.image_url" class="h-full w-full rounded-[50px]"
                                            onerror="(this).src='{{ asset('images/logo.jpg') }}'" alt="" />
                                    </div>
                                </div>
                                <div class="w-20/100 text-gray-500 flex items-center">
                                    <span class="text-sm text-center" x-text="item.title[langLocale]"></span>
                                </div>
                                <div class="w-15/100 text-gray-500 flex items-center">
                                    <span class="text-sm text-center" x-text="item.banner_page ?? '-'"></span>
                                </div>
                                <div class="w-20/100 text-gray-500 flex items-center">
                                    <span class="text-sm text-center truncate" x-text="item.url ?? '-'"></span>
                                </div>
                                <div class="w-10/100 text-gray-500 flex items-center">
                                    <span class="text-sm text-center" x-text="item.ordering ?? '-'"></span>
                                </div>
                                <div class="w-10/100 text-gray-500 flex items-center">
                                    <template x-if="status == active">
                                        <span
                                            class="inline-block whitespace-nowrap rounded-full bg-green-100 px-[0.65em] pt-[0.35em] pb-[0.25em] text-center align-baseline text-[12px] font-bold leading-none text-green-600">
                                            {{ config('dummy.status.active.text') }}
                                        </span>
                                    </template>
                                    <template x-if="status == inactive">
                                        <span
                                            class="inline-block whitespace-nowrap rounded-full bg-red-100 px-[0.65em] pt-[0.35em] pb-[0.25em] text-center align-baseline text-[12px] font-bold leading-none text-red-600">
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
                                        @canany(['banner-update', 'banner-delete'])
                                            <div x-ref="button" x-on:click="toggle()" :aria-expanded="open"
                                                :aria-controls="$id('dropdown-button')" type="button" class="action-btn">
                                                <i data-feather="more-vertical" class="cursor-pointer"></i>
                                            </div>
                                        @endcanany
                                        <ul x-ref="panel" x-show="open" x-transition.origin.top.right
                                            x-on:click.outside="close($refs.button)" :id="$id('dropdown-button')"
                                            style="display: none;" class="absolute right-0 dropdown-menu">
                                            @can('banner-update')
                                                <li x-show="!item.deleted_at">
                                                    <a class="dropdown-item" @click="openStoreBannerDialog(item)">
                                                        <span
                                                            class="material-icons text-violet-600 cursor-pointer">edit</span>
                                                        <span class="text-sm text-gray-600 ml-2">@lang('table.option.edit')</span>
                                                    </a>
                                                </li>
                                                <template x-if="!item.deleted_at && item.status == inactive">
                                                    <li>
                                                        <a class="dropdown-item" @click="onUpdateStatus(item, active)">
                                                            <span
                                                                class="material-icons text-green-500 cursor-pointer">change_circle</span>
                                                            <span
                                                                class="text-sm text-gray-600 ml-2">@lang('table.option.enable')</span>
                                                        </a>
                                                    </li>
                                                </template>
                                                <template x-if="!item.deleted_at && item.status == active">
                                                    <li>
                                                        <a class="dropdown-item" @click="onUpdateStatus(item, inactive)">
                                                            <span
                                                                class="material-icons text-orange-500 cursor-pointer">close</span>
                                                            <span
                                                                class="text-sm text-gray-600 ml-2">@lang('table.option.disable')</span>
                                                        </a>
                                                    </li>
                                                </template>
                                            @endcan
                                            @can('banner-delete')
                                                <template x-if="!item.deleted_at">
                                                    <li>
                                                        <a class="dropdown-item" @click="onDelete(item)">
                                                            <span
                                                                class="material-icons text-red-500 cursor-pointer">delete</span>
                                                            <span
                                                                class="text-sm text-gray-600 ml-2">@lang('table.option.move_to_trash')</span>
                                                        </a>
                                                    </li>
                                                </template>
                                            @endcan
                                            <template x-if="item.deleted_at">
                                                <li>
                                                    <a class="dropdown-item" @click="onRestore(item)">
                                                        <span
                                                            class="material-icons text-green-500 cursor-pointer">replay</span>
                                                        <span
                                                            class="text-sm text-gray-600 ml-2">@lang('table.option.restore')</span>
                                                    </a>
                                                </li>
                                            </template>
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
            'name' => __('table.empty.title', ['name' => __('form.title.banner')]),
            'msg' => __('table.empty.message', ['name' => __('form.title.banner')]),
        ])
        @endcomponent
    </template>
</div>
