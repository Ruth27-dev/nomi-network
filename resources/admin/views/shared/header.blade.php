@section('title')
    | {{ $title ?? '' }}
    {{-- {{ dd(auth()->user()) }} --}}
@stop
<div class="header" x-data="Header">
    <div class="header-wrapper">
        <div class="btn-toggle-sidebar">
            <button class="p-1 mr-4" @click="sidebarOpen = !sidebarOpen">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <span class="!text-gray-600">{{ $header_name ?? '' }}</span>
        </div>

        <span class="right pr-[5px]">
            <div class="language-content">
                @include('admin::components.change-locale')
            </div>
            <div class="profile-info">
                <span class="name text-gray-600">{{ auth()->guard('admin')->user()?->name }}</span>
            </div>
            <div class="btn-logout">
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
                    x-on:focusin.window="! $refs.panel.contains($event.target) && close()" x-id="['dropdown-button']"
                    class="relative dropdown">
                    <div x-ref="button" x-on:click="toggle()" :aria-expanded="open"
                        :aria-controls="$id('dropdown-button')" type="button" class="action-btn">
                        <div class="flex items-center">
                            <img class="image rounded-full w-8 h-8 object-cover" src="{{ auth()->user()->profile_url }}"
                                onerror="this.src='{{ asset('images/profile.png') }}'" alt="">
                            <i class="!w-7 text-gray-600" x-show="open==true" data-feather="chevron-up"></i>
                            <i class="!w-7 text-gray-600" x-show="!open" data-feather="chevron-down"></i>
                        </div>
                    </div>
                    <ul x-ref="panel" x-show="open" x-transition.origin.top.right
                        x-on:click.outside="close($refs.button)" :id="$id('dropdown-button')" style="display: none;"
                        class="absolute right-0 dropdown-menu">
                        <li>
                            <a class="dropdown-item !w-[185px]" @click="profileOpenStoreDialog(auth)">
                                {{-- <i data-feather="edit" class="text-violet-600"></i> --}}
                                <img class="!w-4 !h-4" src="{{ asset('images/icon/pen.png') }}" alt="">
                                <span class="text-black">@lang('message.edit_profile')</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item !w-[185px]" @click="profileOpenChangePasswordDialog(auth)">
                                {{-- <i data-feather="key" class="text-black"></i> --}}
                                <img class="!w-4 !h-4" src="{{ asset('images/icon/lock.png') }}" alt="">
                                <span class="text-black">@lang('message.change_password')</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item !w-[185px]" @click="onSignOut">
                                {{-- <i data-feather="log-out"></i> --}}
                                <img class="!w-4 !h-4" src="{{ asset('images/icon/log-out.png') }}" alt="">
                                <span class="text-black">@lang('message.sign_out.button')</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </span>
    </div>
    <script type="module">
        Alpine.data('Header', () => ({
            auth: @json(auth()->user()),
            table: new Table(),
            notifications: null,
            notifications_un_seen: null,
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
            },
            profileOpenChangePasswordDialog(data) {
                this.$store.profileChangePasswordDialog.open({
                    data: data
                });
            },
            profileOpenStoreDialog(data) {
                this.$store.profileStoreDialog.open({
                    data: data,
                    afterClosed: (res) => {
                        if (res) {
                            location.reload();
                        }
                    }
                });
            },
        }));
    </script>
</div>
