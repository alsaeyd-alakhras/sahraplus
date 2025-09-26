@php
    $title = app()->getLocale() === 'ar' ? 'title_ar' : 'title_en';
    $description = app()->getLocale() === 'ar' ? 'description_ar' : 'description_en';
@endphp
<x-front-layout>
    <!-- Hero Section -->
    <section id="hero" class="overflow-hidden relative h-screen">
        <!-- Slides -->
        <div class="absolute inset-0 opacity-100 hero-slide">
            <img src="{{ $season->poster_full_url }}" alt="Hero Background"
                class="object-cover absolute inset-0 w-full h-full" />
            <div class="absolute inset-0 hero-gradient"></div>
        </div>

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
                        {{ $season->$title }}
                    </div>
                    <p
                        class="mb-6 max-w-xl text-xl leading-relaxed text-gray-200 transition-all duration-300 md:text-lg animate-slide-up {{ $description }}">
                        {{ $season->$description }}
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
        <h2 class="mb-4 text-2xl font-bold text-right">{{__('admin.episodes')}}</h2>
        <div id="movie-sections-container " class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-4">
            @foreach ($season->episodes as $episode)
            <div class="movie-slider-card">
                <img src="{{ $episode->thumbnail_full_url }}" alt="{{ $episode->$title }}"
                    class="object-cover w-full rounded-md aspect-video">
                <div class="movie-slider-details">
                    <h3 class="text-lg font-bold">{{ $episode->$title }}</h3>
                    <div class="movie-slider-line">
                        <span>{{ $episode->air_date }}</span>
                        <span class="text-green-400">•</span>
                        <span>{{ $episode->duration_minutes }}</span>
                        <span class="text-green-400">•</span>
                        <span>{{ $episode->imdb_rating }}</span>
                    </div>
                    <div class="pr-2 text-xs font-bold text-teal-400 border-r-4 border-teal-500">
                        {{ $episode->$description }}
                    </div>
                    <div class="flex items-center space-x-4 rtl:space-x-reverse animate-scale-in">
                        <a href="{{route('site.series.episode.show', $episode->id)}}"
                            class="flex items-center px-4 py-1 space-x-2 font-bold text-white rounded-lg transition-all duration-300 text-[10px] bg-fire-red hover:bg-red-700 btn-glow rtl:space-x-reverse">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                            <span>شاهد الآن</span>
                        </a>
                    </div>
                </div>
            </div>

            @endforeach
        </div>
    </div>
</x-front-layout>
