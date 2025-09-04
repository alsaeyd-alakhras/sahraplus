@include('layouts.partials.front.head', ['title' => "سهرة بلس - تسجيل الدخول" ?? Config::get('app.name'), 'lang' =>  app()->getLocale()])
<style>
    body {
        background: linear-gradient(135deg, #0c0c0c 0%, #1a1a2e 50%, #16213e 100%);
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
    <!-- Main Content -->
    <main class="flex flex-1 justify-center items-center px-4 py-8">
        <div class="w-full max-w-lg">
            <!-- Header -->
            <header class="pt-8 pb-4 text-center">
                <div class="flex flex-col justify-center items-center">
                    <div class="flex justify-center items-center mb-4">
                        @php
                        $logo = $settings['logo_url'] ?? null;
                        @endphp
                        @if($logo)
                            <img src="{{asset('storage/'.$logo)}}" alt="Logo" class="w-32" />
                        @else
                            <h1 class="text-5xl font-black tracking-wider text-white">سهرة بلس</h1>
                        @endif
                    </div>
                    <div class="mx-auto mt-3 w-24 h-1 bg-gradient-to-r from-red-600 to-red-400 rounded-full"></div>
                </div>
            </header>
            @if($errors->any())
            <div class="text-white rounded-lg bg-danger">
                <ul>
                    @foreach ($errors->all() as $key => $error)
                        <li>{{ $key + 1 . ' - ' . $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <!-- Auth Card -->
            <div class="overflow-hidden relative p-8 rounded-3xl card-glow">
                <div class="p-1 mb-3 text-center text-white rounded-2xl btn-gradient">
                    <h1 class="text-2xl font-bold">{{__('admin.admin_page')}}</h1>
                </div>
                <div class="p-1 mb-8 text-center text-white bg-gray-900 rounded-2xl">
                    <h2 class="text-lg font-bold">{{__('admin.login')}}</h2>
                </div>
                <!-- Login Form -->
                <div id="loginForm" class="transition-all duration-700 transform">
                    <form class="space-y-6" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div class="relative">
                                <i
                                    class="absolute right-4 top-1/2 text-gray-400 transform -translate-y-1/2 fas fa-envelope"></i>
                                <input type="username" name="username" value="{{ old('username') }}" placeholder="{{__('admin.email') . ' / ' . __('admin.username')}}" required
                                    class="py-4 pr-12 pl-4 w-full text-white bg-gray-800 rounded-2xl border border-gray-600 transition-all duration-300 outline-none input-glow">
                            </div>
                            <div class="relative">
                                <i
                                    class="absolute right-4 top-1/2 text-gray-400 transform -translate-y-1/2 fas fa-lock"></i>
                                <input type="password" name="password" placeholder="{{__('admin.password')}}" required
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
                                <span class="text-sm">{{__('admin.remember_me')}}</span>
                            </label>
                            {{-- <a href="#"
                                class="text-sm font-medium text-red-400 transition-colors hover:text-red-300">
                                {{__('admin.forgot_password')}}
                            </a> --}}
                        </div>

                        <button type="submit"
                            class="py-4 w-full font-bold text-white rounded-2xl transition-all duration-300 btn-gradient hover:scale-105 hover:shadow-lg">
                            {{__('admin.login')}}
                        </button>
                    </form>
                </div>

                {{-- <!-- Social Login -->
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
                </div> --}}
            </div>
        </div>
    </main>
</div>

<script src="{{asset('assets-site/js/plugins/jquery-3.7.1.min.js')}}"></script>
<script>
    $(document).ready(function () {
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
