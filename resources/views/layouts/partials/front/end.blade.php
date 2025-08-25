    <!-- Js -->
    <script src="{{ asset('assets-site/js/plugins/jquery-3.7.1.min.js') }}"></script>
    <!-- Toastr JS -->
    <script src="{{ asset('assets-site/js/plugins/toastr.min.js') }}"></script>
    <script src="{{ asset('assets-site/js/plugins/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets-site/js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const urlIndex = "{{ route('site.profile.index') }}";
        const urlStore = "{{ route('site.profile.store') }}";
        const urlUpdate = "{{ route('site.profile.update', ':id') }}";
        const urlDestroy = "{{ route('site.profile.destroy', ':id') }}";
        const urlVerifyPin = "{{ route('site.profile.verify-pin', ':id') }}";
        const urlResetPin = "{{ route('site.profile.reset-pin', ':id') }}";
        const require_pin_for_children = {{ config('settings.require_pin_for_children') ? 'true' : 'false' }};
        const auth_user_check = {{ $auth_user ? 'true' : 'false' }};
        const avatarImg = "{{ asset('assets-site/images/avatars/1.jpg') }}";
        const _token = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('assets-site/js/profile.js') }}"></script>
    @stack('scripts')
</body>

</html>
