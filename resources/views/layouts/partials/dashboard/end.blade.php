<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{asset('js/plugins/jquery-3.6.0.min.js')}}"></script>
<script src="{{asset('js/plugins/toastr.min.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>

<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

<!-- endbuild -->
<!-- Vendors JS -->
@stack('vendor-js')

<!-- Main JS -->
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{asset('js/app/toastr.js')}}"></script>
{{-- Custom JS --}}
@stack('scripts')
</body>

</html>
