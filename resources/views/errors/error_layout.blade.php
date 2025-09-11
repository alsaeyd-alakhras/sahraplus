<!DOCTYPE html>
@php
    $lang = app()->getLocale();
@endphp
<html lang="{{ $lang }}" dir="{{ $lang == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title')</title>

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
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'arabic': ['Cairo', 'sans-serif'],
                        'english': ['Inter', 'sans-serif']
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'slideUp': 'slideUp 1s ease-out',
                        'fadeIn': 'fadeIn 1.5s ease-out'
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': {
                                transform: 'translateY(0px)'
                            },
                            '50%': {
                                transform: 'translateY(-10px)'
                            }
                        },
                        glow: {
                            '0%': {
                                filter: 'drop-shadow(0 0 20px rgba(14, 165, 233, 0.3))'
                            },
                            '100%': {
                                filter: 'drop-shadow(0 0 40px rgba(14, 165, 233, 0.6))'
                            }
                        },
                        slideUp: {
                            '0%': {
                                transform: 'translateY(30px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            }
                        },
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            }
                        }
                    }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="{{ asset('assets-site/css/404.css') }}">
</head>

<body class="bg-gradient-to-br from-[#0e1117] via-[#1a1d29] to-[#0e1117] min-h-screen overflow-hidden">
    <!-- Background decoration -->
    <div class="overflow-hidden absolute inset-0">
        <div
            class="absolute -top-40 -right-40 w-80 h-80 bg-purple-500 rounded-full opacity-20 mix-blend-multiply filter blur-xl animate-float">
        </div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-sky-500 rounded-full opacity-20 mix-blend-multiply filter blur-xl animate-float"
            style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-indigo-500 rounded-full opacity-10 mix-blend-multiply filter blur-3xl transform -translate-x-1/2 -translate-y-1/2 animate-float"
            style="animation-delay: 4s;"></div>
    </div>


    @yield('content')
</body>

</html>
