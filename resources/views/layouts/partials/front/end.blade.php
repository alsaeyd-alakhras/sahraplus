    <!-- Js -->
    <script src="{{asset('assets-site/js/plugins/jquery-3.7.1.min.js')}}"></script>
    <!-- Toastr JS -->
    <script src="{{asset('assets-site/js/plugins/toastr.min.js')}}"></script>
    <script src="{{asset('assets-site/js/plugins/swiper-bundle.min.js')}}"></script>
    <script src="{{asset('assets-site/js/script.js')}}"></script>
    <script>
        const profiles = [
            { name: "أبو نايف", img: "{{asset('assets-site/images/avatars/1.jpg')}}", lang: "ar", kids: false },
            { name: "mero", img: "{{asset('assets-site/images/avatars/2.png')}}", lang: "ar", kids: false },
            { name: "الأطفال", img: "{{asset('assets-site/images/avatars/3.png')}}", lang: "ar", kids: true },
            { name: "محمد", img: "{{asset('assets-site/images/avatars/4.png')}}", lang: "ar", kids: false },
        ];
    </script>
    <script src="{{asset('assets-site/js/profile.js')}}"></script>
    @stack('scripts')
</body>

</html>
