<!-- Navigation Header -->
<nav id="navbar"
class="fixed top-0 right-0 left-0 z-[9999] navbar-initial navbar-inset-shadow transition-all duration-500 ease-out">
<div class="container px-6 py-4 mx-auto">
    <div class="flex justify-between items-center">
        <!-- Logo -->
        <div class="flex items-center space-x-4 rtl:space-x-reverse">
            @php
            $logo = $settings['logo_url'] ?? null;
            @endphp
            @if($logo)
                <img src="{{asset('storage/'.$logo)}}" alt="Logo" class="w-32" />
            @else
                <h1 class="text-3xl font-black text-fire-red font-arabic">
                    سهرة بلس
                </h1>
            @endif
        </div>

        <!-- Navigation Links -->
        <div class="hidden items-center space-x-8 md:flex rtl:space-x-reverse">
            <a href="{{route('site.home')}}"
                class="font-medium text-white transition-colors duration-300 hover:text-fire-red">{{__('site.Home')}}</a>
            <a href="{{route('site.series')}}"
                class="font-medium text-white transition-colors duration-300 hover:text-fire-red">{{__('site.Series')}}</a>
            <a href="{{route('site.movies')}}"
                class="font-medium text-white transition-colors duration-300 hover:text-fire-red">{{__('site.Movies')}}</a>
            <a href="{{route('site.live')}}"
                class="font-medium text-white transition-colors duration-300 hover:text-fire-red">{{__('site.Live')}}</a>
            <a href="{{route('site.categories')}}"
                class="font-medium text-white transition-colors duration-300 hover:text-fire-red">{{__('site.Categories')}}</a>
        </div>

        <!-- User Actions -->
        <div class="flex items-center space-x-4 rtl:space-x-reverse">
            <button id="open-search" class="text-white transition-colors duration-300 hover:text-neon-green">
                <i class="fas fa-search"></i>
            </button>
            @guest('web')
            <div class="relative group">
                <button class="flex items-center space-x-2 rtl:space-x-reverse focus:outline-none">
                    <i class="text-white fas fa-language"></i>
                    <i class="text-xs text-white fas fa-chevron-down"></i>
                </button>

                <div class="overflow-hidden absolute left-0 right-auto invisible z-50 mt-3 w-52 text-white bg-gray-800 rounded-lg border-t-4 border-sky-500 shadow-lg opacity-0 transition-all duration-300 transform scale-95 group-hover:opacity-100 group-hover:visible group-hover:scale-100">
                    <div class="px-4 py-2 text-[15px] divide-y divide-gray-700">
                        <div class="py-2 space-y-2">
                            @php
                                $defaultLocale = 'ar';
                                $currentLocale = app()->getLocale();

                                // لو اللغة مش الافتراضية، نشيل prefix
                                if ($currentLocale !== $defaultLocale) {
                                    $currentPath = preg_replace('/^\/[a-z]{2}/', '', request()->getPathInfo());
                                } else {
                                    $currentPath = request()->getPathInfo();
                                }
                            @endphp

                            <a href="/ar{{ $currentPath }}"
                               class="flex justify-start items-center transition-all duration-200 cursor-pointer hover:text-sky-400 hover:pr-2 {{ app()->getLocale() == 'ar' ? 'text-sky-400' : '' }}">
                                <span class="flag flag-ar"></span>
                                <span>العربية</span>
                                @if(app()->getLocale() == 'ar')
                                    <i class="mr-auto fas fa-check rtl:ml-auto"></i>
                                @endif
                            </a>

                            <a href="/en{{ $currentPath }}"
                               class="flex justify-start items-center transition-all duration-200 cursor-pointer hover:text-sky-400 hover:pr-2 {{ app()->getLocale() == 'en' ? 'text-sky-400' : '' }}">
                                <span class="flag flag-en"></span>
                                <span>English</span>
                                @if(app()->getLocale() == 'en')
                                    <i class="mr-auto rtl:ml-auto fas fa-check"></i>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <a href="{{route('login')}}"
                class="font-medium text-white transition-colors duration-300 hover:text-neon-green">
                تسجيل الدخول
            </a>
            <a href="{{route('login')}}"
                class="px-6 py-2 font-medium text-white rounded-lg transition-all duration-300 bg-fire-red hover:bg-red-700 btn-glow">
                اشترك الآن
            </a>
            @endguest
            @auth('web')
            <!-- قسم بيانات اليوزر -->
            <div class="relative group">
                <button class="flex items-center space-x-2 rtl:space-x-reverse focus:outline-none">
                    <span class="font-medium text-white">حسابي</span>
                    <i class="text-xs text-white fas fa-chevron-down"></i>
                </button>

                <!-- Dropdown -->
                <div
                    class="overflow-hidden absolute left-0 right-auto invisible z-50 mt-3 w-52 text-white bg-gray-800 rounded-lg border-t-4 border-sky-500 shadow-lg opacity-0 transition-all duration-300 transform scale-95 group-hover:opacity-100 group-hover:visible group-hover:scale-100">
                    <!-- العناصر -->
                    <div class="px-4 py-2 text-[15px] divide-y divide-gray-700">
                        <div class="py-2 space-y-2">
                            <a href="{{route('site.settings')}}"
                                class="flex justify-start items-center transition-all duration-200 cursor-pointer hover:text-sky-400 hover:pr-2">
                                <i class="pl-2 fas fa-user"></i>
                                <span>إعدادات الحساب</span>
                            </a>

                            <a href="{{route('site.settings',['tab'=>'devices'])}}"
                                class="flex justify-start items-center transition-all duration-200 cursor-pointer hover:text-sky-400 hover:pr-2">
                                <i class="pl-2 fas fa-desktop"></i>
                                <span>إدارة الأجهزة</span>
                            </a>

                            <div
                                class="flex justify-start items-center transition-all duration-200 cursor-pointer hover:text-red-400 hover:pr-2">
                                <form action="{{route('logout')}}" method="POST">
                                    @csrf
                                    <button type="submit">
                                        <i class="pl-2 fas fa-sign-out-alt"></i>
                                        <span>خروج</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative group" id="user-avatar-menu">
                <!-- زر الصورة -->
                <button class="flex items-center space-x-2 rtl:space-x-reverse focus:outline-none">
                    <img src="" alt="Avatar"
                        class="w-9 h-9 rounded-full border-2 border-white shadow profile-img" />
                </button>

                <!-- القائمة الرئيسية -->
                <div id="menu-profile"
                    class="overflow-hidden absolute invisible z-50 mt-3 w-64 text-white bg-gray-800 rounded-lg border-t-4 border-sky-500 shadow-lg opacity-0 transition-all duration-300 transform scale-95 group-hover:opacity-100 group-hover:visible group-hover:scale-100 rtl:left-0 ltr:right-0">

                    <!-- الرأس: الصورة + الاسم + الرابط -->
                    <div class="flex justify-between items-center px-4 py-3 border-b border-gray-700">
                        <div id="openProfileModal"
                            class="flex items-center space-x-2 text-gray-300 transition-all duration-200 rtl:space-x-reverse hover:pr-2 hover:text-sky-400 group">
                            <img src="{{$auth_user->avatar_full_url}}" class="w-10 h-10 rounded-full profile-img" />
                            <div>
                                <p class="text-sm font-bold" id="profile-name">{{$auth_user->full_name}}</p>
                                <p class="text-sm font-bold">تغيير الملف الشخصي</p>
                            </div>
                            <i
                                class="text-gray-300 transition-transform duration-200 fas fa-chevron-left group-hover:-translate-x-1 group-hover:text-sky-400"></i>
                        </div>
                    </div>

                    <!-- القائمة -->
                    <div class="px-4 py-2 space-y-2 text-[15px]">
                        <!-- إدارة الملفات -->
                        <button id="openManageModal"
                            class="flex justify-between items-center transition-all duration-200 cursor-pointer hover:text-sky-400 hover:pr-2">
                            <span>إدارة الملفات</span>
                            <i class="fas fa-paperclip"></i>
                        </button>

                        <!-- اللغة -->
                        <div id="language-toggle"
                            class="flex justify-between items-center pt-1 duration-200 cursor-pointer transition-al2 hover:text-sky-400 hover:pr-2">
                            <div class="flex items-center space-x-1 rtl:space-x-reverse">
                                <i class="fas fa-globe"></i>
                                <span>اللغة</span>
                            </div>
                            <span class="text-xs">({{app()->getLocale()}}) {{app()->getLocale() == 'ar' ? 'العربية' : 'الإنجليزية'}}</span>
                        </div>
                    </div>
                </div>

                <!-- القائمة الفرعية للغات -->
                <div id="menu-language"
                    class="hidden absolute z-50 mt-3 w-64 text-white bg-gray-800 rounded-lg border-t-4 border-sky-500 shadow-lg rtl:left-0 ltr:right-0">

                    <!-- رأس القائمة -->
                    <div class="flex justify-between items-center px-4 py-3 border-b border-gray-700">
                        <span class="font-bold">اللغة</span>
                        <i class="cursor-pointer fas fa-chevron-right" id="back-to-profile"></i>
                    </div>

                    <!-- اللغات -->
                    <div class="px-4 py-2 space-y-2 text-sm">
                        <div
                            class="flex justify-between items-center transition-all duration-200 cursor-pointer hover:text-sky-400">
                            <i class="text-green-500 fas fa-check"></i>
                            <span class="w-full text-right">{{app()->getLocale() == 'ar' ? 'العربية' : 'الإنجليزية'}}</span>
                        </div>
                        @php
                            $defaultLocale = 'ar';
                            $currentLocale = app()->getLocale();

                            // لو اللغة مش الافتراضية، نشيل prefix
                            if ($currentLocale !== $defaultLocale) {
                                $currentPath = preg_replace('/^\/[a-z]{2}/', '', request()->getPathInfo());
                            } else {
                                $currentPath = request()->getPathInfo();
                            }
                        @endphp
                        @if(app()->getLocale() == 'ar')
                        <a href="/en{{ $currentPath }}"
                            class="flex justify-end transition-all duration-200 cursor-pointer hover:text-sky-400">
                            English (En)
                        </a>
                        @else
                        <a href="/ar{{ $currentPath }}"
                            class="flex justify-end transition-all duration-200 cursor-pointer hover:text-sky-400">
                            Arabic (Ar)
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endauth
        </div>
    </div>
</div>
</nav>
<!-- Overlay البحث -->
<div id="search-overlay"
class="flex hidden fixed inset-0 z-50 justify-center items-start px-4 pt-[4rem] backdrop-blur-sm transition-all duration-300 bg-black/60 rtl:pr-6 ltr:pl-6">

<div class="flex justify-between items-center px-8 w-full">
    <!-- صندوق البحث -->
    <div class="relative bg-[#1a1d24] rounded-full flex items-center px-5 py-3 w-full shadow-lg">
        <input type="text" placeholder="مسلسلات، برامج و أفلام"
            class="w-full text-sm placeholder-gray-400 text-right text-white bg-transparent focus:outline-none" />
        <i class="absolute left-4 text-sm text-gray-400 fas fa-search rtl:left-auto rtl:right-[4px]"></i>
    </div>
    <!-- زر الإلغاء -->
    <div class="flex justify-end pr-8 mb-3">
        <button id="close-search" class="text-sm text-white transition hover:text-red-500">إلغاء</button>
    </div>
</div>
<!-- قائمة نتائج البحث -->
<ul id="search-results"
    class="mt-4 bg-[#1a1d24] rounded-lg divide-y divide-gray-700 overflow-hidden text-sm text-white">

    <li class="px-4 py-3 transition cursor-pointer hover:bg-gray-700">
        <a href="#result1" class="block">
            <p class="mb-1 font-bold">فهد البطل</p>
            <p class="text-xs text-gray-400">حفل الفنان فهد الكبيسي وأحدث أعماله</p>
        </a>
    </li>

    <li class="px-4 py-3 transition cursor-pointer hover:bg-gray-700">
        <a href="#result2" class="block">
            <p class="mb-1 font-bold">اسم مسلسل آخر</p>
            <p class="text-xs text-gray-400">وصف مختصر جدًا له يظهر هنا</p>
        </a>
    </li>

</ul>

</div>
