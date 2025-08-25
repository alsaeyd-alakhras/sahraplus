<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Tailwind CSS -->
    {{-- <script src="{{asset('assets-site/js/plugins/tailwindcss-34.js')}}"></script> --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{asset('assets-site/js/tailwind.js')}}"></script>

    <!-- Toastr CSS -->
    <link href="{{asset('assets-site/css/plugins/toastr.min.css')}}" rel="stylesheet" />

    <!-- Swiper -->
    <link rel="stylesheet" href="{{asset('assets-site/css/plugins/swiper-bundle.min.css')}}" />

    <!-- Custom CSS -->
    <link href="{{asset('assets-site/css/style.css')}}" rel="stylesheet" />

    <meta name="user_id" content="{{ $auth_user?->id ?? 0 }}">
    @vite('resources/js/app.js')
    @stack('styles')
</head>

<body class="text-white bg-dark-black">
