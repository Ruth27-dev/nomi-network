<div class="sidebar-wrapper" x-data="sidebar">
    <div class="logo">
        <img src="{{ $company?->image ? $company->image_url : asset('images/logo.jpg') }}" alt="Company Logo">
    </div>
    <div class="menu-list">
        {{-- level 1 --}}
        @foreach ($menu as $key => $item)
            @if (
                !isset($item->permission) ||
                    (isset($item->permission) && auth()->guard('admin')->user()->canany($item->permission)))
                <a class="menu-item {{ routeActive($item->active) ? 'active' : '' }}"
                    :class="{ show_active: show_menu.includes({{ $item->id }}) }"
                    @if ($item->key != 'sign_out')
                        @if ($item->path && $item->children->count() == 0)
                            href="{!! url($item->path) !!}"
                        @else
                            @click="toggleMenu({{ $item->id }})"
                        @endif
                    @else
                        @click="onSignOut()"
                    @endif
                    x-init="initializeMenu({{ $item->id }}, {{ routeActive($item->active) }})">
                    <div class="menu-text">
                        <i class="material-icons-outlined">{{ $item->icon }}</i>
                        <div style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                            <span>{{ json_decode($item->name)->{app()->getLocale()} }}</span>
                        </div>
                        @if ($item->children->count() > 0)
                            <p :class="{ show: show_menu.includes({{ $item->id }}) }">
                                <i data-feather="chevron-down" class="angle-icon"></i>
                            </p>
                        @endif
                    </div>
                </a>
                {{-- level 2 --}}
                @if ($item->children->count() > 0)
                    <div class="sub-menu" x-show="show_menu.includes({{ $item->id }})" style="display:none"
                        x-transition:enter.duration.300ms>
                        @foreach ($item->children as $child)
                            @if (isset($child->permission) && auth()->guard('admin')->user()->canany($child->permission))
                                <a class="sub-item child-menu {{ routeActive($child->active) ? 'active' : '' }}"
                                    :class="{ show_active: child_show_menu.includes({{ $child->id }}) }"
                                    @if ($child->path && $child->children->count() == 0)
                                        href="{!! url($child->path) !!}"
                                    @else
                                        @click="toggleChildMenu({{ $child->id }})"
                                    @endif
                                    x-init="initializeChildMenu({{ $child->id }}, {{ routeActive($child->active) }})">
                                    <div class="sub-item-text">
                                        @if ($child->icon)
                                            <i class="material-icons-outlined">{{ $child->icon }}</i>
                                        @else
                                            <i data-feather="disc"></i>
                                        @endif
                                        <div
                                            style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                                            <span>{{ json_decode($child->name)->{app()->getLocale()} }}</span>
                                        </div>
                                        @if ($child->children->count() > 0)
                                            <div class="dropdown"
                                                :class="{ show: child_show_menu.includes({{ $child->id }}) }">
                                                <i data-feather="chevron-down" class="angle-icon"></i>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                                {{-- Grandchildren --}}
                                @if ($child->children->count() > 0)
                                    <div class="sub-menu" x-show="child_show_menu.includes({{ $child->id }})"
                                        style="display:none" x-transition:enter.duration.300ms>
                                        @foreach ($child->children as $grand_child)
                                            @if (isset($grand_child->permission) && auth()->guard('admin')->user()->canany($grand_child->permission))
                                                <a class="sub-item child-menu {{ routeActive($grand_child->active) ? 'active' : '' }}"
                                                    :class="{
                                                        show_active: grand_child_show_menu.includes(
                                                            {{ $grand_child->id }})
                                                    }"
                                                    href="{!! url($grand_child->path) !!}"
                                                    x-init="initializeGrandChildMenu({{ $grand_child->id }}, {{ routeActive($grand_child->active) }})">
                                                    <div class="sub-item-text">
                                                        @if ($grand_child->icon)
                                                            <i
                                                                class="material-icons-outlined">{{ $grand_child->icon }}</i>
                                                        @else
                                                            <i data-feather="disc"></i>
                                                        @endif
                                                        <div
                                                            style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                                                            <span>{{ json_decode($grand_child->name)->{app()->getLocale()} }}</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                @endif
            @endif
        @endforeach
    </div>
</div>

<script type="module">
    Alpine.data("sidebar", () => ({
        show_menu: [],
        child_show_menu: [],
        grand_child_show_menu: [],

        // Toggle level 1 menu
        toggleMenu(key) {
            if (this.show_menu.includes(key)) {
                this.show_menu = this.show_menu.filter(id => id !== key);
            } else {
                this.show_menu.push(key);
            }
        },
        initializeMenu(key, def) {
            if (def) this.show_menu.push(key);
        },

        // Toggle level 2 menu
        toggleChildMenu(key) {
            if (this.child_show_menu.includes(key)) {
                this.child_show_menu = this.child_show_menu.filter(id => id !== key);
            } else {
                this.child_show_menu.push(key);
            }
        },
        initializeChildMenu(key, def) {
            if (def) this.child_show_menu.push(key);
        },

        // Toggle level 3 menu
        initializeGrandChildMenu(key, def) {
            if (def) this.grand_child_show_menu.push(key);
        },

        onSignOut() {
            this.$store.confirmDialog.open({
                data: {
                    title: "@lang('message.sign_out.title')",
                    message: "@lang('message.sign_out.message')",
                    btnClose: "@lang('message.close')",
                    btnSave: "@lang('message.sign_out.button')",
                },
                afterClosed: (res) => {
                    if (res) {
                        location.href = "{{ route('admin-sign-out') }}";
                    }
                }
            });
        }
    }));
</script>
