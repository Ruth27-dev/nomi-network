<template x-dialog="viewVariationDialog">
    <div x-data="viewVariationDialog" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 class="text-gray-600">@lang('table.option.view_variation')</h3>
                <span @click="$dialog('viewVariationDialog').close()"><i data-feather="x"></i></span>
            </div>
            <div class="form-body flex-auto overflow-y-auto pr-3">
                <div class="table-wrapper">
                    <div class="table-header">
                        <div class="flex flex-col flex-auto">
                            <div class="w-full flex gap-3">
                                <div class="flex-auto border-t-[1px] border-b-[1px] border-gray-200 bg-gray-50">
                                    <div class="flex h-11">
                                        <div class="w-5/100 text-sm font-bold text-gray-500 grid place-items-center">
                                            <span>@lang('table.field.no')</span>
                                        </div>
                                        <div class="w-15/100 text-sm font-bold text-gray-500 flex items-center">
                                            <span>@lang('table.field.image')</span>
                                        </div>
                                        <div class="w-15/100 text-sm font-bold text-gray-500 flex items-center">
                                            <span>@lang('table.field.title')</span>
                                        </div>
                                        <div class="w-20/100 text-sm font-bold text-gray-500 grid place-items-center">
                                            <span>@lang('table.field.description')</span>
                                        </div>
                                          <div class="w-15/100 text-sm font-bold text-gray-500 grid place-items-center">
                                            <span>@lang('table.field.note')</span>
                                        </div>
                                        <div class="w-15/100 text-sm font-bold text-gray-500 grid place-items-center">
                                            <span>@lang('table.field.size')</span>
                                        </div>
                                        <div class="w-15/100 text-sm font-bold text-gray-500 grid place-items-center">
                                            <span>@lang('table.field.price')</span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-body !w-full !p-0">
                        <template x-for="(item,index) in data">
                            <div class="w-full flex gap-3 h-[70px]">
                                <div class="flex-auto border-b-[1px] border-gray-200">
                                    <div class="flex row-item h-full hover:bg-gray-50 cursor-pointer">
                                        <div class="w-5/100 grid place-items-center text-gray-500">
                                            <span class="text-sm" x-text="index + 1"></span>
                                        </div>
                                        <div class="w-15/100 text-gray-500 flex items-center">
                                            <div @click="onViewImage(item?.images[0]?.url)"
                                                class="cursor-pointer h-[50px] w-[50px]">
                                                <img x-bind:src="item?.images[0]?.url"
                                                    class="h-full w-full rounded-[50px]"
                                                    onerror="(this).src='{{ asset('images/no.jpg') }}'"
                                                    alt="" />
                                            </div>
                                        </div>
                                        <div class="w-15/100 text-gray-500 flex items-center">
                                            <span class="text-sm text-center"
                                                x-text="item.title ? item.title?.[langLocale] : '-'"></span>
                                        </div>
                                        <div class="w-20/100 text-gray-500 grid place-items-center">
                                            <span class="text-sm text-center"
                                                x-text="item.description ? item.description?.[langLocale] : '-'"></span>
                                        </div>
                                             <div class="w-15/100 text-gray-500 grid place-items-center">
                                            <span class="text-sm text-center"
                                                x-text="item.note ? item.note?.[langLocale] : '-'"></span>
                                        </div>
                                        <div class="w-15/100 text-gray-500 grid place-items-center">
                                            <span class="text-sm text-center"
                                                x-text="item.size ? item.size : '-'"></span>
                                        </div>
                                        <div class="w-15/100 text-gray-500 grid place-items-center">
                                             <span class="text-sm text-center"
                                                x-text="item.price ? item.price : '-'"></span>
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
            </div>

        </form>
    </div>
    <script>
        Alpine.data('viewVariationDialog', () => ({

            dialogData: null,
            validate: null,
            loading: false,
            data: null,
            async init() {
                this.dialogData = this.$dialog('viewVariationDialog').data;
                feather.replace();
                if (this.dialogData?.id) {
                    await this.fetchDataForUpdate(Number(this.dialogData?.id), (res) => {
                        this.data = res?.data.product_variations;
                        console.log(this.data);

                    });
                    this.setValue(this.data);
                }
            },
            onViewImage(path) {
                const thumbnail = Fancybox.show([{
                    src: path,
                    type: "image",
                }, ], {
                    on: {
                        ready: (fancybox) => {
                            document.querySelector('.fancybox__container').style.zIndex = this
                                .$store.libs.getLastIndex() + 1;
                        },
                    }
                });
            },
            async fetchDataForUpdate(id, callback) {
                await Axios({
                    url: `{{ route('admin-product-detail') }}`,
                    method: 'GET',
                    params: {
                        id: id,
                    }
                }).then((res) => {
                    callback(res.data)
                }).catch((e) => {
                    console.log(e);
                });
            },
        }));
    </script>
</template>
