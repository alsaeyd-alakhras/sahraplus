<x-front-layout>
    @php
        $title = app()->getLocale() === 'ar' ? 'title_ar' : 'title_en';
        $description = app()->getLocale() === 'ar' ? 'description_ar' : 'description_en';
    @endphp
    <!-- Hero Section -->
    <section id="hero" class="overflow-hidden relative h-screen">
        <!-- Slides -->
        <div class="absolute inset-0 opacity-100 hero-slide">
            <img src="{{ $series->backdrop_full_url }}" alt="Hero Background"
                class="object-cover absolute inset-0 w-full h-full" />
            <video src="{{ $series->trailer_full_url }}" class="hidden object-cover absolute inset-0 w-full h-full"
                playsinline></video>
            <div class="absolute inset-0 hero-gradient"></div>
        </div>

        <!-- Mute/Unmute Button -->
        <button id="muteBtn" class="absolute left-10 top-28 z-20 p-3 text-white rounded-full bg-black/60"
            data-state="muted">
            <svg class="w-6 h-6 mute-icon" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z">
                </path>
            </svg>
        </button>

        <!-- Hero Content -->
        <div class="flex relative z-10 items-center h-full">
            <div class="container px-6 mx-auto">
                <div
                    class="max-w-[25rem] opacity-80 transition-all duration-500 ease-in-out transform translate-x-0 hero-content hover:opacity-100 hover:-translate-x-10">
                    <div class="mb-8 h-[80px] logo-wrapper transition-all duration-500 ease-in-out">
                        <img src="" alt="logo"
                            class="object-contain h-full transition-all duration-300 hover:scale-125" />
                    </div>
                    <div class="text-base text-gray-400 transition-all duration-300 episode animate-slide-up">
                        {{ $series->$title }}
                    </div>
                    <p
                        class="mb-6 max-w-xl text-xl leading-relaxed text-gray-200 transition-all duration-300 md:text-lg animate-slide-up {{ $description }}">
                        {{ $series->$description }}
                    </p>
                    <div
                        class="flex flex-wrap items-center mb-6 space-x-3 text-sm text-gray-400 rtl:space-x-reverse tags animate-slide-up">
                        <!-- يتم توليدها ديناميكيًا -->
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Movie Sections Container -->
    <div class="container mx-auto">
        <h2 class="mb-4 text-2xl font-bold text-right">{{__('admin.seasons')}}</h2>
        <div id="movie-sections-container " class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-4">
            @foreach ($series->seasons as $season)
                @include('site.partials.section_season_series', compact('season'))
            @endforeach
        </div>
    </div>
</x-front-layout>
