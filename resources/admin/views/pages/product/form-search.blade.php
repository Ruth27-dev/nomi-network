<template x-if="showDialogSearch">
    <div class="fixed inset-0 flex items-start justify-center bg-black/20 z-[100] pt-[5vh]">
        <div style="border-radius:8px"
            class=" min-w-[800px] px-6 py-4 mx-auto text-left bg-white rounded shadow-lg mt-[145px]"
            x-transition:enter="motion-safe:ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100">
            <div class="overflow-auto">
                <div class="form-admin !w-full h-full">
                    <form class="form-wrapper flex flex-col h-full" @submit.prevent>
                        <div class="form-header">
                            <h3 class="!text-[18px]">@lang('form.name.search')</h3>
                        </div>
                        <div class="form-body flex-auto overflow-y-auto">
                            <div class="form-row">
                                <label>@lang('form.body.label.search')</label>
                                <input type="text" name="search" x-model="formFilter.search" placeholder="..." autocomplete="off">
                            </div>
         
                            <div class="row-2">
                                <div class="form-row">
                                    <label>@lang('form.body.label.category')</label>
                                    <select class="w-full flex items-center justify-between"
                                        name="category_id" x-model="formFilter.category_id">
                                        <option value="">...</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->title['en'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-footer mb-[11px]">
                            <div class="form-button flex">
                                <button type="button" class="!text-[#f00c] border-none !rounded !border-[#f00c]"
                                    @click="showDialogSearch = false">
                                    <i data-feather="x"></i>
                                    <span>@lang('form.button.close')</span>
                                </button>
                                <button type="button" class="!rounded" color="primary" @click="onFilter()">
                                    <i data-feather="search"></i>
                                    <span>@lang('form.button.search')</span>
                                </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
