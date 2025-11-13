<x-front-layout>
    @php
        $name = 'name_' . app()->getLocale();
        $title = 'title_' . app()->getLocale();
        $description = 'description_' . app()->getLocale();
    @endphp

    <!-- Category Header -->
    <div class="px-4 pt-[6rem] pb-8 text-white max-w-[95%] mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-4xl font-bold">{{ $category->$name }}</h1>
            <a href="{{ route('site.categories') }}" class="px-4 py-2 text-sm transition-colors bg-gray-800 rounded-lg hover:bg-gray-700">
                العودة للتصنيفات
            </a>
        </div>
        
        @if($category->$description)
            <p class="mb-8 text-lg text-gray-400">{{ $category->$description }}</p>
        @endif

        <!-- Movies Section -->
        @if($category->movies->isNotEmpty())
            <div class="mb-12">
                <h2 class="mb-6 text-2xl font-bold">الأفلام</h2>
                <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                    @foreach($category->movies as $movie)
                        <a href="{{ route('site.movie.show', $movie->slug) }}" class="group">
                            <div class="overflow-hidden relative rounded-lg transition-transform duration-300 group-hover:scale-105">
                                <img src="{{ $movie->poster_full_url }}" 
                                    alt="{{ $movie->$title }}" 
                                    class="object-cover w-full aspect-[2/3]">
                                <div class="flex absolute inset-0 justify-center items-center bg-black bg-opacity-0 transition-all duration-300 group-hover:bg-opacity-70">
                                    <svg class="w-16 h-16 text-white transition-transform duration-300 transform scale-0 group-hover:scale-100" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z" />
                                    </svg>
                                </div>
                                @if($movie->imdb_rating)
                                    <div class="flex absolute top-2 left-2 gap-1 items-center px-2 py-1 text-xs font-bold bg-yellow-500 rounded">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        {{ number_format($movie->imdb_rating, 1) }}
                                    </div>
                                @endif
                            </div>
                            <h3 class="mt-2 text-sm font-semibold line-clamp-2">{{ $movie->$title }}</h3>
                            <p class="text-xs text-gray-400">
                                @if($movie->release_date)
                                    {{ $movie->release_date->format('Y') }}
                                @endif
                                @if($movie->duration_minutes)
                                    • {{ $movie->duration_minutes }} دقيقة
                                @endif
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Series Section -->
        @if($category->series->isNotEmpty())
            <div class="mb-12">
                <h2 class="mb-6 text-2xl font-bold">المسلسلات</h2>
                <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                    @foreach($category->series as $series)
                        <a href="{{ route('site.series.show', $series->slug) }}" class="group">
                            <div class="overflow-hidden relative rounded-lg transition-transform duration-300 group-hover:scale-105">
                                <img src="{{ $series->poster_full_url }}" 
                                    alt="{{ $series->$title }}" 
                                    class="object-cover w-full aspect-[2/3]">
                                <div class="flex absolute inset-0 justify-center items-center bg-black bg-opacity-0 transition-all duration-300 group-hover:bg-opacity-70">
                                    <svg class="w-16 h-16 text-white transition-transform duration-300 transform scale-0 group-hover:scale-100" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z" />
                                    </svg>
                                </div>
                                @if($series->imdb_rating)
                                    <div class="flex absolute top-2 left-2 gap-1 items-center px-2 py-1 text-xs font-bold bg-yellow-500 rounded">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        {{ number_format($series->imdb_rating, 1) }}
                                    </div>
                                @endif
                                <div class="absolute top-2 right-2 px-2 py-1 text-xs font-bold text-white bg-green-600 rounded">
                                    مسلسل
                                </div>
                            </div>
                            <h3 class="mt-2 text-sm font-semibold line-clamp-2">{{ $series->$title }}</h3>
                            <p class="text-xs text-gray-400">
                                @if($series->first_air_date)
                                    {{ $series->first_air_date->format('Y') }}
                                @endif
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if($category->movies->isEmpty() && $category->series->isEmpty())
            <div class="py-16 text-center">
                <p class="text-xl text-gray-400">لا يوجد محتوى في هذا التصنيف حالياً</p>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            // Category show page scripts
        </script>
    @endpush
</x-front-layout>

