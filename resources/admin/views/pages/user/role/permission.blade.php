<template x-dialog="userPermission">
    <div x-data="userPermission" class="form-admin !w-full h-full">
        <form class="form-wrapper flex flex-col h-full">
            <div class="form-header">
                <h3 class="text-gray-600">
                    @lang('form.title.set_permission_to_role') (<span x-text="dialogData?.display_name?.[langLocale]"></span>)
                </h3>
                <span @click="$dialog('userPermission').close()"><i data-feather="x"></i></span>
            </div>
            <span class="text-red-500 text-sm" x-show="validate?.permissions" x-text="validate?.permissions"></span>
            <div class="form-body flex-auto overflow-y-auto !pb-2" style="overflow-x: hidden; padding-top: unset;">
                <div class="permissionLayoutGp">
                    <template x-for="(module,index) in modulePermissions">
                        <div class="permissionControl !mt-[5px]">
                            <div class="permissionLayout">
                                <div class="permissionItem">
                                    <div class="permissionHeader arrowPermission !gap-2">
                                        <span class="material-icons parent-module text-gray-600">expand_more</span>
                                        <div class="textItem text-gray-500 font-bold"
                                            x-text="getItemByLang(module?.display_name, langLocale, arrayLangLocale)">
                                        </div>
                                        <label class="form-check-label" :for="'chk-permission-group-' + module?.id">
                                            <div class="inputItem">
                                                <input type="checkbox" @click="parentModuleCheckAll($el)"
                                                    class="cursor-pointer role_permission permissionAllitem permission-group"
                                                    :id="'chk-permission-group-' + module?.id"
                                                    :data-permission-id="module?.id" :checked="module?.check" />
                                            </div>
                                        </label>
                                    </div>
                                    <template x-if="module.sub_modules.length > 0">
                                        <template x-for="(sub_module,index) in module?.sub_modules">
                                            <div class="permissionListItemSubModule">
                                                <div class="permissionHeader arrowPermission !gap-2">
                                                    <span x-show="sub_module?.permissions?.length > 0"
                                                        class="material-icons sub-module !text-gray-600">expand_more</span>
                                                    <div class="textItem text-gray-600" style="font-size: 14px;"
                                                        x-text="getItemByLang(sub_module?.display_name, langLocale, arrayLangLocale)">
                                                    </div>
                                                    <label class="form-check-label"
                                                        :for="'chk-permission-group-' + sub_module?.id">
                                                        <div class="inputItem">
                                                            <input type="checkbox" @click="subModuleCheckAll($el)"
                                                                class="cursor-pointer role_permission permissionAllitem permission-group"
                                                                :id="'chk-permission-group-' + sub_module?.id"
                                                                :data-sub-permission-id="sub_module?.id"
                                                                :class="'permission-item-' + module?.id"
                                                                :checked="sub_module?.check" />
                                                        </div>
                                                    </label>
                                                </div>
                                                <div class="permissionListItemPermission">
                                                    <template x-for="(action,key) in sub_module?.permissions">
                                                        <label class="permissionItemCh"
                                                            :for="'permission' + action?.name">
                                                            <i
                                                                class="material-icons !text-gray-500">fiber_manual_record</i>
                                                            <div class="textItem !text-gray-600 !ml-2"
                                                                style="font-size: 14px;"
                                                                x-text="getItemByLang(action?.display_name, langLocale, arrayLangLocale)">
                                                            </div>
                                                            <div class="inputItem">
                                                                <input type="checkbox"
                                                                    class="permissionAllitem cursor-pointer"
                                                                    name="permission" :value="action?.name"
                                                                    :class="['permission-item-' + module?.id,
                                                                        'sub-permission-item-' + sub_module?.id
                                                                    ]"
                                                                    :id="'permission' + action?.name"
                                                                    :checked="action?.check" />
                                                            </div>
                                                        </label>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </template>
                                    <div class="permissionListItemGpCh">
                                        <template x-for="(action,key) in module?.permissions">
                                            <label class="permissionItemCh" :for="'permission' + action?.name">
                                                <i class="material-icons !text-gray-500">fiber_manual_record</i>
                                                <div class="textItem !text-gray-600 !ml-2" style="font-size: 14px;"
                                                    x-text="getItemByLang(action?.display_name, langLocale, arrayLangLocale)">
                                                </div>
                                                <div class="inputItem">
                                                    <input type="checkbox" class="permissionAllitem cursor-pointer"
                                                        name="permission" :value="action?.name"
                                                        :class="'permission-item-' + module?.id"
                                                        :id="'permission' + action?.name" :checked="action?.check" />
                                                </div>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="form-footer">
                <div class="form-button">
                    <button type="button" color="primary" @click="onSave()" :disabled="loading">
                        <span class="material-icons m-1">double_arrow</span>
                       <span>@lang('form.button.assign_permission')</span>
                        <div class="loader" style="display: none" x-show="loading"></div>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        Alpine.data('userPermission', () => ({
            loading: false,
            validate: null,
            dialogData: null,
            modulePermissions: [],
            permissions: [],
            async init() {
                this.dialogData = this.$dialog('userPermission').data;
                feather.replace();
                await this.fetchModulePermission((res) => {
                    setTimeout(() => {
                        let arrow = document.querySelectorAll('.arrowPermission');
                        for (var i = 0; i < arrow.length; i++) {
                            arrow[i].addEventListener('click', (e) => {
                                let arrowParent = e.target.parentElement
                                    .parentElement;
                                arrowParent.classList.toggle('showMenu');
                            });
                        }
                    }, 100);
                });
            },
            async fetchModulePermission(callback) {
                await this.$http({
                    url: `{{ route('admin-user-role-fetch-module-permission') }}`,
                    params: {
                        role_id: this.dialogData?.id
                    },
                    success: (res) => {
                        this.modulePermissions = res.data;
                        callback(res.data);
                    }
                });
            },
            parentModuleCheckAll(el) {
                let permission_id = $(el).data('permission-id');
                if (el.checked == true) {
                    $(`.permission-item-${permission_id}`).each(function(index, element) {
                        $(element).prop('checked', true);
                    });
                } else {
                    $(`.permission-item-${permission_id}`).each(function(index, element) {
                        $(element).prop('checked', false);
                    });
                }
            },
            subModuleCheckAll(el) {
                let permission_id = $(el).data('sub-permission-id');
                if (el.checked == true) {
                    $(`.sub-permission-item-${permission_id}`).each(function(index, element) {
                        $(element).prop('checked', true);
                    });
                } else {
                    $(`.sub-permission-item-${permission_id}`).each(function(index, element) {
                        $(element).prop('checked', false);
                    });
                }
            },
            permissionData() {
                this.permissions = $('input[name="permission"]:checked').map(function() {
                    return $(this).val();
                }).get();
            },
            onSave() {
                this.permissionData();
                this.$store.confirmDialog.open({
                    data: {
                        title: "@lang('dialog.title')",
                        message: "@lang('dialog.msg.assign_permission')",
                        btnClose: "@lang('dialog.button.close')",
                        btnSave: "@lang('dialog.button.save')",
                    },
                    afterClosed: (result) => {
                        if (result) {
                            this.loading = true;
                            Axios({
                                url: `{{ route('admin-user-role-assign-permission') }}`,
                                method: 'POST',
                                data: {
                                    role_id: this.dialogData?.id,
                                    permissions: this.permissions,
                                }
                            }).then((res) => {
                                if (res.data.error == false) {
                                    this.$dialog('userPermission').close(true);
                                }
                                Toast({
                                    message: res.data.message,
                                    status: res.data.status,
                                    size: 'small',
                                });
                            }).catch((e) => {
                                this.validate = e.response.data.errors;
                            }).finally(() => {
                                this.loading = false;
                            });
                        }
                    }
                });
            }
        }));
    </script>
</template>
