@php
    $name = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
    $title = app()->getLocale() === 'ar' ? 'title_ar' : 'title_en';
    $description = app()->getLocale() === 'ar' ? 'description_ar' : 'description_en';
@endphp
<div class="movie-slider-card">
    <img src="{{ $season->poster_full_url }}" alt="{{ $season->$title }}"
        class="object-cover w-full rounded-md aspect-video">
    <div class="movie-slider-details">
        <h3 class="text-lg font-bold">{{ $season->$title }}</h3>
        <div class="movie-slider-line">
            <span>{{ $season->air_date }}</span>
        </div>
        <div class="pr-2 text-xs font-bold text-teal-400 border-r-4 border-teal-500">
            {{ $season->$description }}
        </div>
        <div class="flex items-center space-x-4 rtl:space-x-reverse animate-scale-in">
            <a href="{{route('site.series.season.show', $season->id)}}"
                class="flex items-center px-4 py-1 space-x-2 font-bold text-white rounded-lg transition-all duration-300 text-[10px] bg-fire-red hover:bg-red-700 btn-glow rtl:space-x-reverse">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z" />
                </svg>
                <span>شاهد الآن</span>
            </a>
        </div>
    </div>
</div>
