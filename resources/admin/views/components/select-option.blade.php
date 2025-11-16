<template x-data="{}" x-if="$store.components.active">
    <div class="dialog" x-data="selectOption" x-bind:style="{ zIndex: $store.libs.getLastIndex() + 1 }">
        <div class="dialog-container">
            <div class="select-option" id="select-option" style="width: 350px">
                <div class="select-option-header">
                    <h3 x-text="options?.title"></h3>
                    <button x-show="options?.allow_close" style="display: none" class="btn-close"
                        @click="close()">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="select-option-body">
                    <div class="form-row no-label">
                        <input x-on:input="onInput($event)" type="text" name="search"
                            x-bind:placeholder="options?.placeholder" autocomplete="off">
                    </div>
                    <template x-if="data?.length > 0">
                        <div class="data-list">
                            <template x-for="(item,index) in data">
                                <div class="data-list-item" x-bind:class="{ selected: isSelected(item) }"
                                    @click="onSelect(item)">
                                    <template x-if="options.multiple">
                                        <div class="selected-file-icon">
                                            <template x-if="isSelected(item)">
                                                <div class="selected" x-text="selectedIndex(item)"></div>
                                            </template>
                                            <template x-if="!isSelected(item)">
                                                <div></div>
                                            </template>
                                        </div>
                                    </template>
                                    <div class="img" x-show="item._image">
                                        <img x-bind:src="item._image" x-bind:alt="item._image"
                                            x-on:error="onImageError">
                                    </div>
                                    <div class="title">
                                        <div class="flex items-center">
                                            <p x-text="item._title"></p>
                                        </div>
                                        <div class="desc_price">
                                            <span class="!text-red-600" x-show="item._price && !item._label_price" x-text="item?._price"></span>
                                            <span class="!text-red-600" x-show="item._price && item._label_price" x-text="item._label_price + item?._price"></span>
                                            <span x-show="item._description" x-text="item._description"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!loading && (!data || data?.length == 0)">
                        @component('admin::components.empty',
                            [
                                'name' => 'No data found!',
                                'msg' => 'Sorry, please try other keyword.',
                                'style' => 'padding: 50px 0',
                                'image_style' => 'height: 120px',
                            ])
                        @endcomponent
                    </template>
                    <template x-if="loading">
                        @include('admin::components.progress-bar')
                    </template>
                </div>
                <div class="select-option-footer">
                    <!-- button for unselect -->
                    <template x-if="!options.multiple">
                        <button type="button" @click="onUnselect()"
                            x-bind:disabled="!selected || selected.length == 0">
                            Unselect
                        </button>
                    </template>

                    <!-- button for unselect multiple -->
                    <template x-if="options.multiple && unselect">
                        <button type="button" @click="onUnselectMultiple(selected)"
                             class="!mr-3 !bg-red-700">
                            Unselect
                        </button>
                    </template>

                    <!-- button for save -->
                    <template x-if="options.multiple">
                        <button type="button" @click="onClose(selected)"
                            x-bind:disabled="!selected || selected.length == 0">
                            Save (<span x-text="selected?.length || 0"></span>)
                        </button>
                    </template>
                </div>
            </div>
        </div>
        <script>
            Alpine.data('selectOption', () => ({
                data: null,
                loading: true,
                options: null,
                unselect: false,
                selected: [],
                init() {
                    this.options = Alpine.store('components').options;
                    this.unselect = this.options.unselect;
                    this.data = this.options.data;
                    this.selected = this.options.selected;

                    Alpine.store('animate').enter(this.$root.children[0], () => {
                        feather.replace();
                        this.onReady();
                    });
                },
                onReady() {
                    this.$store.components.options.onReady((data) => {
                        if (!data) return;
                        this.loading = false;
                        this.data = data;
                    });
                },
                onImageError(e) {
                    e.target.src = `{{ asset('images/profile.png') }}`;
                },
                onInput(e) {
                    this.data = [];
                    this.loading = true;
                    this.$store.components.options.onSearch(e.target.value, (data) => {
                        if (!data) return;
                        this.loading = false;
                        this.data = data;
                    });
                },
                onSelect(data) {
                    if (this.options.multiple) {
                        if (this.isSelected(data)) {
                            this.selected = this.selected.filter(item => item._id !== data._id);
                        } else {
                            this.selected.push(data);
                        }
                    } else {
                        this.onClose(data);
                    }
                },
                isSelected(data, call_back) {
                    if (this.options.multiple) {
                        return this.selected?.find(item => item._id == data._id) ? call_back ?? true : false;
                    } else {
                        return this.selected == data._id ? call_back ?? true : false;
                    }
                },
                selectedIndex(data) {
                    return this.selected.findIndex(item => item._id == data._id) + 1;
                },
                onUnselect() {
                    this.selected = null;
                    this.onClose();
                },
                onUnselectMultiple(data) {
                    let index = this.selected.findIndex(item => item._id == data._id);
                    data.forEach(item => {
                        index = this.selected.findIndex(i => i._id == item._id);
                    });
                    this.selected.splice(index, 1);
                    this.selectedIndex(null);
                    this.isSelected(data, false);
                    this.onClose();
                },
                onClose(data = null) {
                    if (typeof this.$store.components.options.beforeClose === 'undefined') {
                        this.save(data);
                        return;
                    }
                    this.$store.components.options.beforeClose(data, (close) => {
                        if (close) {
                            this.save(data);
                        }
                    });
                },
                save(data = null) {
                    Alpine
                        .store('animate')
                        .leave(this.$root.children[0], () => {
                            this.$store.components.active = false;
                            this.$store.components.options.afterClose(data);
                        });
                },
                close() {
                    Alpine
                        .store('animate')
                        .leave(this.$root.children[0], () => {
                            this.$store.components.active = false;
                        });
                }
            }));
        </script>
    </div>
</template>
<script type="module">
    Alpine.store('components', {
        active: false,
        options: {
            data: null,
            unselect: false,
            selected: null,
            multiple: false,
            title: 'Choose an option',
            placeholder: 'Type to search...',
            allow_close: true,
            onReady: () => {},
            onSearch: () => {},
            // beforeClose: () => {},
            afterClose: () => {},
        },
    });
    window.SelectOption = (options) => {
        Alpine.store('components', {
            active: true,
            options: {
                ...Alpine.store('components').options,
                ...options,
            }
        });
    };
</script>
