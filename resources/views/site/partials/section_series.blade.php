@php
    $name = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
@endphp
<div class="overflow-visible mb-6 px-4 py-6 mx-auto max-w-[95%]">
    <!-- عنوان القسم -->
    <h2 class="mb-4 text-2xl font-bold text-right">{{$title_section}}</h2>

    <!-- سلايدر Swiper -->
    <div style="z-index: {{1000 - $index_section}}"
    class="overflow-visible relative pb-44 swiper {{$display_type == 'vertical' ? 'mySwiper-vertical' : 'mySwiper-horizontal'}}">
        <div class="swiper-wrapper" >
            @php
                $items = collect($items)->map(function ($i) {
                    // حوّل الـ item نفسه لـ object
                    $i = (object) $i;

                    // لو فيه categories حوّلها لـ Collection of Objects
                    if (isset($i->categories)) {
                        $i->categories = collect($i->categories)->map(fn($c) => (object) $c);
                    }else{
                        $i->categories = collect();
                    }
                    return $i;
                });
            @endphp
            @foreach ($items as $item)
                <div class="swiper-slide">
                    <div class="movie-slider-card">
                        <img src="{{ $item->backdrop_full_url ?? $item->poster_full_url }}" alt="{{ $item->title }}"
                            class="object-cover w-full rounded-md aspect-video">
                        <div class="movie-slider-details">
                            <h3 class="text-lg font-bold">{{ $item->view_count }}</h3>
                            <div class="movie-slider-line">
                                <span>{{ $item->duration_minutes }}</span>
                                <span class="text-green-400">•</span>
                                <span>{{ $item->imdb_rating }}</span>
                                <span class="text-green-400">•</span>
                                <span>{{ $item->language }}</span>
                            </div>
                            <div class="movie-slider-line">
                                @foreach ($item->categories as $category)
                                    <span>{{ $category->$name }}</span>
                                    @if (!$loop->last)
                                        <span class="text-green-400" >•</span>
                                    @endif
                                @endforeach
                            </div>
                            <div class="pr-2 text-xs font-bold text-teal-400 border-r-4 border-teal-500">
                                {{ $item->title }}
                            </div>
                            <div class="flex items-center space-x-4 rtl:space-x-reverse animate-scale-in">
                                <button
                                    class="flex items-center px-1 py-1 text-lg font-bold text-white bg-gray-800 bg-opacity-80 rounded-full transition-all duration-300 hover:bg-red-700 btn-glow rtl:space-x-reverse">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </button>
                                <a href="{{route('site.series.show', $item->slug)}}"
                                    class="flex items-center px-4 py-1 space-x-2 font-bold text-white rounded-lg transition-all duration-300 text-[10px] bg-fire-red hover:bg-red-700 btn-glow rtl:space-x-reverse">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z" />
                                    </svg>
                                    <span>شاهد الآن</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- الأسهم -->
        <div class="text-white swiper-button-next"></div>
        <div class="text-white swiper-button-prev"></div>
    </div>
</div>
