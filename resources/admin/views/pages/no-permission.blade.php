@extends('admin::shared.layout')
@section('layout')
    <div class="no-permission-container" x-data="permission">
        <div class="no-permission-wrapper">
            <div class="image">
                <img src="{!! asset('images/permission.png') !!}" alt="">
            </div>
            <div class="message">
                <h1>403</h1>
                <span class="title">No Permission</span>
                <span class="des">You do not have permission to access this page.</span>
                <button @click="onSignOut()">
                    <span>Sign Out</span>
                </button>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="module">
        Alpine.data('permission', () => ({
            onSignOut() {
                this.$store.confirmDialog.open({
                    data: {
                        title: "Sign Out",
                        message: "Are you want to Sign Out?",
                        btnClose: "Close",
                        btnSave: "Sign Out",
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
@endsection
