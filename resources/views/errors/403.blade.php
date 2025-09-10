@extends('errors.error_layout')

@section('title', __('site.403_title'))
@section('content')
    <!-- Main Content -->
    <div class="flex relative z-10 flex-col justify-center items-center px-6 min-h-screen text-center">
        <!-- Error Icon -->
        <div class="animate-fadeIn">
            <i class="mb-8 text-6xl text-yellow-400 fas fa-exclamation-triangle animate-glow"></i>
        </div>

        <!-- 3D Error Number -->
        <div
            class="error-number text-[120px] sm:text-[150px] lg:text-[200px] xl:text-[250px] font-extrabold bg-gradient-to-r from-sky-400 via-purple-500 to-pink-500 text-transparent bg-clip-text animate-float leading-none mb-4">
            403
        </div>

        <!-- Error Messages -->
        <div id="errorMessages" class="animate-slideUp">
            <div class="text-center">
                <h1 class="mb-4 text-2xl font-bold text-white sm:text-3xl lg:text-4xl font-arabic">
                    {{__('site.403_title')}}
                </h1>
            </div>
        </div>

        <!-- Action Buttons -->
        <div id="actionButtons" class="flex flex-col gap-4 sm:flex-row animate-slideUp" style="animation-delay: 0.3s;">
            <div class="flex flex-col gap-4 sm:flex-row">
                <a href="{{route('site.home')}}"
                    class="px-8 py-4 font-semibold text-white bg-gradient-to-r from-sky-600 to-blue-600 rounded-full transition-all duration-300 transform group hover:from-sky-700 hover:to-blue-700 hover:scale-105 hover-glow font-english">
                    <i class="mr-2 fas fa-home"></i>
                    {{__('site.error_button')}}
                </a>
                <button onclick="history.back()"
                    class="px-8 py-4 font-semibold text-white rounded-full border transition-all duration-300 transform group glass-effect hover:bg-white/10 hover:scale-105 hover-glow font-english">
                    <i class="mr-2 fas fa-arrow-left"></i>
                    {{__('site.error_back')}}
                </button>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="mt-12 animate-slideUp" style="animation-delay: 0.6s;">
            <div id="additionalInfo">
                <div class="text-sm text-gray-500 font-arabic">
                    <p>{{__('site.error_code_time')}} <span id="currentTime">{{Carbon\Carbon::now()}}</span></p>
                </div>
            </div>
        </div>
    </div>
@endsection
