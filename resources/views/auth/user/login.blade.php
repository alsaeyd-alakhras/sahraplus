@php
    $locale = app()->getLocale();
    $siteName = $locale === 'ar'
        ? ($settings['site_name_ar'] ?? 'سهرة بلس')
        : ($settings['site_name_en'] ?? config('settings.app_name'));
    $pageTitle = $siteName . ' - ' . __('admin.login');
@endphp
@include('layouts.partials.front.head', ['title' => $pageTitle, 'lang' =>  $locale])
<style>
    body {
        background: url("{{ asset('assets-site/images/login_banner.jpg') }}");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /* background: linear-gradient(135deg, #0c0c0c 0%, #1a1a2e 50%, #16213e 100%); */
        min-height: 100vh;
    }

    .card-glow {
        box-shadow: 0 0 50px rgba(220, 38, 38, 0.1);
        backdrop-filter: blur(20px);
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .btn-gradient {
        background: linear-gradient(45deg, #1f1f1f, #dc2626, #1f1f1f);
        background-size: 300% 300%;
        animation: gradientShift 3s ease infinite;
    }

    @keyframes gradientShift {

        0%,
        100% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }
    }

    .tab-active {
        background: linear-gradient(90deg, #dc2626, #ef4444);
        color: white;
    }

    .tab-inactive {
        background: rgba(255, 255, 255, 0.05);
        color: #9ca3af;
    }

    .input-glow:focus {
        box-shadow: 0 0 20px rgba(220, 38, 38, 0.4);
        border-color: #dc2626;
    }

    .floating-shapes {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: -1;
    }

    .shape {
        position: absolute;
        background: rgba(220, 38, 38, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    .shape:nth-child(1) {
        width: 80px;
        height: 80px;
        top: 20%;
        left: 10%;
        animation-delay: 0s;
    }

    .shape:nth-child(2) {
        width: 120px;
        height: 120px;
        top: 60%;
        right: 15%;
        animation-delay: 2s;
    }

    .shape:nth-child(3) {
        width: 60px;
        height: 60px;
        bottom: 20%;
        left: 20%;
        animation-delay: 4s;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px) rotate(0deg);
        }

        50% {
            transform: translateY(-20px) rotate(180deg);
        }
    }
</style>

<!-- Floating Background Shapes -->
<div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
</div>

<div class="flex flex-col min-h-screen">
    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $key => $error)
                <li>{{ $key + 1 . ' - ' . $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <!-- Main Content -->
    <main class="flex flex-1 justify-center items-center px-4 py-8">
        <div class="w-full max-w-lg">
            <!-- Header -->
            <header class="pt-8 pb-4 text-center">
                <div class="flex flex-col justify-center items-center">
                    <div class="flex justify-center items-center mb-4">
                        @php
                            $logoPath = $settings['logo_url'] ?? null;
                            $logoUrl = null;

                            if ($logoPath) {
                                if (\Illuminate\Support\Str::startsWith($logoPath, ['http://', 'https://'])) {
                                    $logoUrl = $logoPath;
                                } else {
                                    $logoUrl = asset('storage/' . ltrim($logoPath, '/'));
                                }
                            }
                        @endphp
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="w-32" />
                        @else
                            <h1 class="text-5xl font-black tracking-wider text-white">{{ $siteName }}</h1>
                        @endif
                    </div>
                    <div class="mx-auto mt-3 w-24 h-1 bg-gradient-to-r from-red-600 to-red-400 rounded-full"></div>
                </div>
            </header>
            <!-- Auth Card -->
            <div class="overflow-hidden relative p-8 rounded-3xl card-glow">
                <!-- Tabs -->
                <div class="flex p-1 mb-8 bg-gray-900 rounded-2xl">
                    <button id="loginTab"
                        class="flex-1 px-6 py-4 text-sm font-bold rounded-xl transition-all duration-500 transform tab-active">
                        تسجيل الدخول
                    </button>
                    <button id="registerTab"
                        class="flex-1 px-6 py-4 text-sm font-bold rounded-xl transition-all duration-500 transform tab-inactive">
                        إنشاء حساب جديد
                    </button>
                </div>

                <!-- Login Form -->
                <div id="loginForm" class="transition-all duration-700 transform">
                    <form class="space-y-6" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div class="relative">
                                <i
                                    class="absolute right-4 top-1/2 text-gray-400 transform -translate-y-1/2 fas fa-envelope"></i>
                                <input type="email" name="username" value="{{ old('username') }}" placeholder="البريد الإلكتروني" required
                                    class="py-4 pr-12 pl-4 w-full text-white bg-gray-800 rounded-2xl border border-gray-600 transition-all duration-300 outline-none input-glow">
                            </div>
                            <div class="relative">
                                <i
                                    class="absolute right-4 top-1/2 text-gray-400 transform -translate-y-1/2 fas fa-lock"></i>
                                <input type="password" name="password" placeholder="كلمة المرور" required
                                    class="py-4 pr-12 pl-12 w-full text-white bg-gray-800 rounded-2xl border border-gray-600 transition-all duration-300 outline-none input-glow">
                                <button type="button"
                                    class="absolute left-4 top-1/2 text-gray-400 transition-colors transform -translate-y-1/2 hover:text-white">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <label class="flex items-center text-gray-300 cursor-pointer">
                                <input type="checkbox" class="ml-2 w-4 h-4 accent-red-600">
                                <span class="text-sm">تذكرني</span>
                            </label>
                            {{-- <a href="#"
                                class="text-sm font-medium text-red-400 transition-colors hover:text-red-300">
                                نسيت كلمة المرور؟
                            </a> --}}
                        </div>

                        <button type="submit"
                            class="py-4 w-full font-bold text-white rounded-2xl transition-all duration-300 btn-gradient hover:scale-105 hover:shadow-lg">
                            دخول الآن
                        </button>
                    </form>
                </div>

                <!-- Register Form -->
                <div id="registerForm" class="hidden transition-all duration-700 transform">
                    <form class="space-y-6" action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="relative">
                                    <input type="text" name="first_name" placeholder="الاسم الأول" required
                                        class="px-4 py-4 w-full text-white bg-gray-800 rounded-2xl border border-gray-600 transition-all duration-300 outline-none input-glow">
                                </div>
                                <div class="relative">
                                    <input type="text" name="last_name" placeholder="الاسم الأخير"
                                        class="px-4 py-4 w-full text-white bg-gray-800 rounded-2xl border border-gray-600 transition-all duration-300 outline-none input-glow">
                                </div>
                            </div>
                            <div class="relative">
                                <i
                                    class="absolute right-4 top-1/2 text-gray-400 transform -translate-y-1/2 fas fa-envelope"></i>
                                <input type="email" name="email" placeholder="البريد الإلكتروني" required
                                    class="py-4 pr-12 pl-4 w-full text-white bg-gray-800 rounded-2xl border border-gray-600 transition-all duration-300 outline-none input-glow">
                            </div>
                            <div class="relative">
                                <i
                                    class="absolute right-4 top-1/2 text-gray-400 transform -translate-y-1/2 fas fa-phone"></i>
                                <input type="tel" name="phone" placeholder="رقم الهاتف"
                                    class="py-4 pr-12 pl-4 w-full text-white bg-gray-800 rounded-2xl border border-gray-600 transition-all duration-300 outline-none input-glow">
                            </div>
                            <div class="relative">
                                <i
                                    class="absolute right-4 top-1/2 text-gray-400 transform -translate-y-1/2 fas fa-lock"></i>
                                <input type="password" name="password" placeholder="كلمة المرور" required
                                    class="py-4 pr-12 pl-12 w-full text-white bg-gray-800 rounded-2xl border border-gray-600 transition-all duration-300 outline-none input-glow">
                                <button type="button"
                                    class="absolute left-4 top-1/2 text-gray-400 transition-colors transform -translate-y-1/2 hover:text-white">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="relative">
                                <i
                                    class="absolute right-4 top-1/2 text-gray-400 transform -translate-y-1/2 fas fa-lock"></i>
                                <input type="password" name="password_confirmation" placeholder="تأكيد كلمة المرور" required
                                    class="py-4 pr-12 pl-12 w-full text-white bg-gray-800 rounded-2xl border border-gray-600 transition-all duration-300 outline-none input-glow">
                                <button type="button"
                                    class="absolute left-4 top-1/2 text-gray-400 transition-colors transform -translate-y-1/2 hover:text-white">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="text-sm">
                            <label class="flex items-start text-gray-300 cursor-pointer" >
                                <input type="checkbox" class="mt-1 ml-2 w-4 h-4 accent-red-600" required>
                                <span>أوافق على <a href="#" class="text-red-400 underline hover:text-red-300">شروط
                                        الاستخدام</a> و <a href="#"
                                        class="text-red-400 underline hover:text-red-300">سياسة الخصوصية</a></span>
                            </label>
                        </div>

                        <button type="submit"
                            class="py-4 w-full font-bold text-white rounded-2xl transition-all duration-300 btn-gradient hover:scale-105 hover:shadow-lg">
                            إنشاء الحساب
                        </button>
                    </form>
                </div>

                <!-- Social Login -->
                <div class="mt-8">
                    <div class="flex relative justify-center items-center mb-6">
                        <div class="w-full border-t border-gray-600"></div>
                        <span class="px-4 text-sm font-medium text-center text-gray-400 bg-gray-800">أو استخدم</span>
                        <div class="w-full border-t border-gray-600"></div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <button
                            class="flex justify-center items-center py-3 text-white bg-blue-600 rounded-xl transition-all duration-300 hover:bg-blue-700 hover:scale-105">
                            <i class="text-lg fab fa-facebook-f"></i>
                        </button>
                        <button
                            class="flex justify-center items-center py-3 text-white bg-red-500 rounded-xl transition-all duration-300 hover:bg-red-600 hover:scale-105">
                            <i class="text-lg fab fa-google"></i>
                        </button>
                        <button
                            class="flex justify-center items-center py-3 text-white bg-gray-700 rounded-xl transition-all duration-300 hover:bg-gray-600 hover:scale-105">
                            <i class="text-lg fab fa-apple"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-16 bg-gradient-to-r from-gray-900 via-black to-gray-900">
        <div class="container px-6 mx-auto">
            <!-- Main Footer Content -->
            <div class="grid grid-cols-1 gap-8 mb-12 md:grid-cols-2 lg:grid-cols-4">
                <!-- Brand Section -->
                <div class="text-center md:text-right">
                    <h3 class="mb-4 text-2xl font-bold text-white">سهرة بلس</h3>
                    <p class="text-sm leading-relaxed text-gray-400">
                        منصتك المفضلة للترفيه والمحتوى المميز
                    </p>
                    <div
                        class="mx-auto mt-4 w-16 h-1 bg-gradient-to-r from-red-600 to-red-400 rounded-full md:mx-0">
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="text-center md:text-right">
                    <h4 class="mb-4 text-lg font-bold text-white">روابط سريعة</h4>
                    <ul class="space-y-3">
                        <li><a href="#"
                                class="inline-block text-gray-400 transition-all duration-300 hover:text-red-400 hover:translate-x-1">الرئيسية</a>
                        </li>
                        <li><a href="#"
                                class="inline-block text-gray-400 transition-all duration-300 hover:text-red-400 hover:translate-x-1">المحتوى</a>
                        </li>
                        <li><a href="#"
                                class="inline-block text-gray-400 transition-all duration-300 hover:text-red-400 hover:translate-x-1">من
                                نحن</a></li>
                        <li><a href="#"
                                class="inline-block text-gray-400 transition-all duration-300 hover:text-red-400 hover:translate-x-1">اتصل
                                بنا</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div class="text-center md:text-right">
                    <h4 class="mb-4 text-lg font-bold text-white">الدعم والمساعدة</h4>
                    <ul class="space-y-3">
                        <li><a href="#"
                                class="inline-block text-gray-400 transition-all duration-300 hover:text-red-400 hover:translate-x-1">مركز
                                خدمة العملاء</a></li>
                        <li><a href="#"
                                class="inline-block text-gray-400 transition-all duration-300 hover:text-red-400 hover:translate-x-1">سياسة
                                الخصوصية</a></li>
                        <li><a href="#"
                                class="inline-block text-gray-400 transition-all duration-300 hover:text-red-400 hover:translate-x-1">الشروط
                                والأحكام</a></li>
                    </ul>
                </div>

                <!-- Social Media -->
                <div class="text-center md:text-right">
                    <h4 class="mb-4 text-lg font-bold text-white">تابعنا</h4>
                    <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                        <a href="#"
                            class="flex justify-center items-center w-12 h-12 text-white bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl transition-all duration-300 group hover:from-blue-500 hover:to-blue-600 hover:scale-110 hover:rotate-6">
                            <i class="transition-transform fab fa-facebook-f group-hover:scale-125"></i>
                        </a>
                        <a href="#"
                            class="flex justify-center items-center w-12 h-12 text-white bg-gradient-to-br from-gray-700 to-gray-800 rounded-xl transition-all duration-300 group hover:from-gray-600 hover:to-gray-700 hover:scale-110 hover:rotate-6">
                            <i class="transition-transform fab fa-twitter group-hover:scale-125"></i>
                        </a>
                        <a href="#"
                            class="flex justify-center items-center w-12 h-12 text-white bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-xl transition-all duration-300 group hover:from-pink-400 hover:via-purple-400 hover:to-indigo-400 hover:scale-110 hover:rotate-6">
                            <i class="transition-transform fab fa-instagram group-hover:scale-125"></i>
                        </a>
                        <a href="#"
                            class="flex justify-center items-center w-12 h-12 text-white bg-gradient-to-br from-blue-400 to-blue-500 rounded-xl transition-all duration-300 group hover:from-blue-300 hover:to-blue-400 hover:scale-110 hover:rotate-6">
                            <i class="transition-transform fab fa-telegram group-hover:scale-125"></i>
                        </a>
                        <a href="#"
                            class="flex justify-center items-center w-12 h-12 text-white bg-gradient-to-br from-gray-800 to-black rounded-xl transition-all duration-300 group hover:from-gray-700 hover:to-gray-800 hover:scale-110 hover:rotate-6">
                            <i class="transition-transform fab fa-tiktok group-hover:scale-125"></i>
                        </a>
                        <a href="#"
                            class="flex justify-center items-center w-12 h-12 text-white bg-gradient-to-br from-red-600 to-red-700 rounded-xl transition-all duration-300 group hover:from-red-500 hover:to-red-600 hover:scale-110 hover:rotate-6">
                            <i class="transition-transform fab fa-youtube group-hover:scale-125"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="pt-8 border-t border-gray-700">
                <div class="flex flex-col justify-start items-center md:flex-row">
                    <p class="mb-4 text-sm text-gray-400 md:mb-0">
                        © 2025 سهرة بلس - جميع الحقوق محفوظة
                    </p>
                </div>
            </div>
        </div>
    </footer>
</div>

<script src="{{asset('assets-site/js/plugins/jquery-3.7.1.min.js')}}"></script>
<script>
    $(document).ready(function () {
        // Tab Switching
        $('#loginTab').click(function () {
            $(this).removeClass('tab-inactive').addClass('tab-active');
            $('#registerTab').removeClass('tab-active').addClass('tab-inactive');
            $('#loginForm').removeClass('hidden').addClass('block');
            $('#registerForm').removeClass('block').addClass('hidden');
        });

        $('#registerTab').click(function () {
            $(this).removeClass('tab-inactive').addClass('tab-active');
            $('#loginTab').removeClass('tab-active').addClass('tab-inactive');
            $('#registerForm').removeClass('hidden').addClass('block');
            $('#loginForm').removeClass('block').addClass('hidden');
        });

        // Password Toggle
        $(document).on('click', '.fa-eye, .fa-eye-slash', function () {
            const input = $(this).closest('.relative').find('input');
            const currentType = input.attr('type');

            if (currentType === 'password') {
                input.attr('type', 'text');
                $(this).removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                $(this).removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    });
</script>
</body>

</html>
