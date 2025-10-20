<x-front-layout>
    @php
        $name = 'name_' . app()->getLocale();
        $title = 'title_' . app()->getLocale();
    @endphp
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
        <link rel="stylesheet" href="{{ asset('assets-site/css/actor.css') }}">
    @endpush

    <div class="container px-6 mx-auto max-w-6xl pt-[78px]">
        <!-- Header Section -->
        <section class="mb-12 actor-header">
            <div class="bg-[#1c1f26] rounded-2xl p-8 border border-gray-800 shadow-2xl">
                <div class="flex flex-col gap-8 items-center lg:flex-row lg:items-start">
                    <!-- Actor Image -->
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <img src="{{ $cast->photo_full_url ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=300&fit=crop&crop=face' }}"
                                alt="{{ $cast->$name }}"
                                class="object-cover w-48 h-48 rounded-2xl border-4 border-sky-500 shadow-lg">
                            <div
                                class="absolute -bottom-2 -right-2 bg-green-500 w-8 h-8 rounded-full border-4 border-[#1c1f26] flex items-center justify-center">
                                <i class="text-xs text-white fas fa-check"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Actor Info -->
                    <div class="flex-1 text-center lg:text-right">
                        <h1
                            class="mb-4 text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-blue-500 lg:text-5xl">
                            {{ $cast->$name }}
                        </h1>

                        <div class="flex flex-wrap gap-4 justify-center mb-4 text-gray-300 lg:justify-end">
                            <span class="flex gap-2 items-center">
                                <i class="text-sky-400 fas fa-flag"></i>
                                <span>{{ $cast->nationality }}</span>
                            </span>
                            <span class="flex gap-2 items-center">
                                <i class="text-sky-400 fas fa-calendar"></i>
                                <span>
                                    @if ($cast->birth_date)
                                        {{ $cast->birth_date->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </span>
                            <span class="flex gap-2 items-center">
                                <i class="text-sky-400 fas fa-venus-mars"></i>
                                <span>
                                    @php
                                        $g = strtolower((string) $cast->gender);
                                        $genderLabel = $g === 'male' ? 'ذكر' : ($g === 'female' ? 'أنثى' : 'غير محدد');
                                    @endphp
                                    {{ $genderLabel }}
                                </span>
                            </span>
                        </div>

                        <!-- Biography -->
                        <!-- Biography -->
                        @php
                            $plainBio = trim(strip_tags((string) ($cast->bio ?? '')));
                            $hasMoreBio = mb_strlen($plainBio) > 300;
                            $bioFirst = $hasMoreBio ? mb_substr($plainBio, 0, 300) : $plainBio;
                            $bioRest = $hasMoreBio ? mb_substr($plainBio, 300) : '';
                        @endphp
                        <div class="relative mb-6">
                            <p class="text-lg leading-relaxed text-gray-400" id="bioText">
                                <span>{{ $bioFirst }}</span>
                                @if ($hasMoreBio)
                                    <span id="bioMore" class="hidden">{{ $bioRest }}</span>
                                @endif
                            </p>
                            @if ($hasMoreBio)
                                <button id="toggleBio" class="mt-2 text-sky-400 transition-colors hover:text-sky-300">
                                    <span>قراءة المزيد</span>
                                    <i class="mr-1 fas fa-chevron-down"></i>
                                </button>
                            @endif
                        </div>
                        {{-- 
                        <!-- Social Media -->
                        <div class="flex gap-4 justify-center lg:justify-end">
                            <a href="#"
                                class="bg-[#2a2d35] hover:bg-sky-500 w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300">
                                <i class="text-lg fab fa-facebook-f"></i>
                            </a>
                            <a href="#"
                                class="bg-[#2a2d35] hover:bg-sky-500 w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300">
                                <i class="text-lg fab fa-twitter"></i>
                            </a>
                            <a href="#"
                                class="bg-[#2a2d35] hover:bg-sky-500 w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300">
                                <i class="text-lg fab fa-instagram"></i>
                            </a>
                            <a href="#"
                                class="bg-[#2a2d35] hover:bg-sky-500 w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300">
                                <i class="text-lg fab fa-youtube"></i>
                            </a> --}}
                    </div>
                </div>
            </div>
        </section>

        <!-- Actor Info Grid -->
        <section class="mb-12 actor-info">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Profession -->
                <div
                    class="bg-[#1c1f26] rounded-xl p-6 border border-gray-800 hover:border-sky-500 transition-all duration-300">
                    <div class="flex gap-4 items-center">
                        <div class="flex justify-center items-center w-12 h-12 bg-sky-500 rounded-full">
                            <i class="text-lg text-white fas fa-theater-masks"></i>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-400">المهنة</h3>
                            @php
                                $roleTypeFirst = null;
                                foreach ($cast->movies ?? [] as $m) {
                                    if (isset($m->pivot) && $m->pivot->role_type) {
                                        $roleTypeFirst = $m->pivot->role_type;
                                        break;
                                    }
                                }
                                if (!$roleTypeFirst) {
                                    foreach ($cast->series ?? [] as $s) {
                                        if (isset($s->pivot) && $s->pivot->role_type) {
                                            $roleTypeFirst = $s->pivot->role_type;
                                            break;
                                        }
                                    }
                                }
                                $roleMap = ['actor' => 'ممثل', 'director' => 'مخرج', 'writer' => 'كاتب'];
                                $profession = $roleTypeFirst
                                    ? $roleMap[strtolower($roleTypeFirst)] ?? $roleTypeFirst
                                    : '—';
                            @endphp
                            <p class="text-lg font-semibold text-white">{{ $profession }}</p>
                        </div>
                    </div>
                </div>

                <!-- Number of Works -->
                <div
                    class="bg-[#1c1f26] rounded-xl p-6 border border-gray-800 hover:border-sky-500 transition-all duration-300">
                    <div class="flex gap-4 items-center">
                        <div class="flex justify-center items-center w-12 h-12 bg-green-500 rounded-full">
                            <i class="text-lg text-white fas fa-film"></i>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-400" data-en="Works">الأعمال</h3>
                            @php
                                $moviesCount = method_exists($cast, 'movies') ? $cast->movies()->count() : 0;
                                $seriesCount = method_exists($cast, 'series') ? $cast->series()->count() : 0;
                                $totalWorks = $moviesCount + $seriesCount;
                            @endphp
                            <p class="text-lg font-semibold text-white">{{ $totalWorks }} عمل (أفلام:
                                {{ $moviesCount }} / مسلسلات: {{ $seriesCount }})</p>
                        </div>
                    </div>
                </div>

                <!-- Awards -->
                <div
                    class="bg-[#1c1f26] rounded-xl p-6 border border-gray-800 hover:border-sky-500 transition-all duration-300">
                    <div class="flex gap-4 items-center">
                        <div class="flex justify-center items-center w-12 h-12 bg-yellow-500 rounded-full">
                            <i class="text-lg text-white fas fa-trophy"></i>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-400">الجوائز</h3>
                            <p class="text-lg font-semibold text-white">-</p>
                        </div>
                    </div>
                </div>

                <!-- Active Years -->
                <div
                    class="bg-[#1c1f26] rounded-xl p-6 border border-gray-800 hover:border-sky-500 transition-all duration-300">
                    <div class="flex gap-4 items-center">
                        <div class="flex justify-center items-center w-12 h-12 bg-purple-500 rounded-full">
                            <i class="text-lg text-white fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-400" data-en="Active Years">سنوات النشاط</h3>
                            @php
                                $years = [];
                                foreach ($cast->movies ?? [] as $m) {
                                    if (!empty($m->release_date)) {
                                        $years[] = $m->release_date->year;
                                    }
                                }
                                foreach ($cast->series ?? [] as $s) {
                                    if (!empty($s->first_air_date)) {
                                        $years[] = $s->first_air_date->year;
                                    }
                                    if (!empty($s->last_air_date)) {
                                        $years[] = $s->last_air_date->year;
                                    }
                                }
                                $activeYears = count($years) ? min($years) . ' - ' . max($years) : '—';
                            @endphp
                            <p class="text-lg font-semibold text-white">{{ $activeYears }}</p>
                        </div>
                    </div>
                </div>

                <!-- Rating -->
                <div
                    class="bg-[#1c1f26] rounded-xl p-6 border border-gray-800 hover:border-sky-500 transition-all duration-300">
                    <div class="flex gap-4 items-center">
                        <div class="flex justify-center items-center w-12 h-12 bg-orange-500 rounded-full">
                            <i class="text-lg text-white fas fa-star"></i>
                        </div>
                        <div>
                            <h3 class="text-sm text-gray-400">التقييم</h3>
                            @php
                                $movieAvg = collect($cast->movies ?? [])
                                    ->pluck('imdb_rating')
                                    ->filter()
                                    ->avg();
                                $seriesAvg = collect($cast->series ?? [])
                                    ->pluck('imdb_rating')
                                    ->filter()
                                    ->avg();
                                $avgRating = collect([$movieAvg, $seriesAvg])
                                    ->filter()
                                    ->avg();
                                $avgRating = $avgRating ? number_format($avgRating, 1) : '—';
                            @endphp
                            <div class="star-rating">
                                <i class="fas fa-star star active"></i>
                                <i class="fas fa-star star active"></i>
                                <i class="fas fa-star star active"></i>
                                <i class="fas fa-star star active"></i>
                                <i class="fas fa-star star active"></i>
                                <span class="mr-2 font-semibold text-white">{{ $avgRating }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Genres -->
                <div
                    class="bg-[#1c1f26] rounded-xl p-6 border border-gray-800 hover:border-sky-500 transition-all duration-300">
                    <div class="flex gap-4 items-start">
                        <div class="flex flex-shrink-0 justify-center items-center w-12 h-12 bg-red-500 rounded-full">
                            <i class="text-lg text-white fas fa-tags"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="mb-2 text-sm text-gray-400" data-en="Genres">الأنواع</h3>
                            <h3 class="mb-2 text-sm text-gray-400">الأنواع/أبرز ما يعرف به</h3>
                            @php $knownFor = is_array($cast->known_for) ? $cast->known_for : []; @endphp
                            <div class="flex flex-wrap gap-2">
                                @forelse($knownFor as $tag)
                                    <span
                                        class="px-2 py-1 text-xs text-white bg-sky-500 rounded-full genre-tag">{{ $tag }}</span>
                                @empty
                                    <span class="text-sm text-gray-400">—</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Works Section -->
        <section class="actor-works">
            <div class="bg-[#1c1f26] rounded-2xl p-8 border border-gray-800">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-bold" data-en="Filmography">الأعمال السينمائية</h2>
                    <div class="flex gap-2">
                        <button class="px-4 py-2 text-sm text-white bg-sky-500 rounded-lg filter-btn active"
                            data-filter="all" data-en="All">الكل</button>
                        <button
                            class="filter-btn bg-[#2a2d35] hover:bg-sky-500 text-white px-4 py-2 rounded-lg text-sm transition-all"
                            data-filter="movie" data-en="Movies">أفلام</button>
                        <button
                            class="filter-btn bg-[#2a2d35] hover:bg-sky-500 text-white px-4 py-2 rounded-lg text-sm transition-all"
                            data-filter="series" data-en="Series">مسلسلات</button>
                    </div>
                </div>

                <!-- Swiper Container -->
                <div class="swiper worksSwiper">
                    <div class="swiper-wrapper">
                        @php
                            $roleLabel = function ($type) {
                                $t = strtolower((string) $type);
                                return [
                                    'actor' => 'تمثيل',
                                    'director' => 'إخراج',
                                    'writer' => 'كتابة',
                                ][$t] ?? $type;
                            };
                        @endphp

                        @foreach ($cast->movies ?? [] as $movie)
                            <div class="swiper-slide">
                                <div class="work-card bg-[#2a2d35] rounded-xl overflow-hidden shadow-lg"
                                    data-type="movie">
                                    <div class="relative">
                                        <img src="{{ $movie->poster_full_url ?? 'https://images.unsplash.com/photo-1489599735734-79b4622c580e?w=300&h=400&fit=crop' }}"
                                            alt="{{ $movie->$title }}" class="object-cover w-full h-64">
                                        <div
                                            class="absolute top-4 right-4 px-2 py-1 text-xs text-white bg-sky-500 rounded-full">
                                            <span>فيلم</span>
                                        </div>
                                    </div>
                                    <div class="p-6">
                                        <h3 class="mb-2 text-lg font-semibold">{{ $movie->$title }}</h3>
                                        <div class="flex justify-between items-center mb-3 text-sm text-gray-400">
                                            <span>{{ $movie->release_date?->format('Y') ?? '—' }}</span>
                                            <span>{{ $movie->language ?? ($movie->country ?? '') }}</span>
                                        </div>
                                        <p class="mb-4 text-sm text-gray-400">
                                            {{ $roleLabel($movie->pivot->role_type ?? null) }}
                                            @if (!empty($movie->pivot->character_name))
                                                - {{ $movie->pivot->character_name }}
                                            @endif
                                        </p>
                                        <a href="{{ route('site.movie.show', $movie->slug) }}"
                                            class="py-2 w-full text-white bg-sky-500 rounded-lg transition-all duration-300 hover:bg-sky-600">
                                            <i class="mr-2 fas fa-play"></i>
                                            <span>مشاهدة</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @foreach ($cast->series ?? [] as $series)
                            <div class="swiper-slide">
                                <div class="work-card bg-[#2a2d35] rounded-xl overflow-hidden shadow-lg"
                                    data-type="series">
                                    <div class="relative">
                                        <img src="{{ $series->poster_full_url ?? 'https://images.unsplash.com/photo-1440404653325-ab127d49abc1?w=300&h=400&fit=crop' }}"
                                            alt="{{ $series->$title }}" class="object-cover w-full h-64">
                                        <div
                                            class="absolute top-4 right-4 px-2 py-1 text-xs text-white bg-green-500 rounded-full">
                                            <span>مسلسل</span>
                                        </div>
                                    </div>
                                    <div class="p-6">
                                        <h3 class="mb-2 text-lg font-semibold">{{ $series->$title }}</h3>
                                        <div class="flex justify-between items-center mb-3 text-sm text-gray-400">
                                            <span>{{ $series->first_air_date?->format('Y') ?? '—' }}</span>
                                            <span>{{ $series->language ?? ($series->country ?? '') }}</span>
                                        </div>
                                        <p class="mb-4 text-sm text-gray-400">
                                            {{ $roleLabel($series->pivot->role_type ?? null) }}
                                            @if (!empty($series->pivot->character_name))
                                                - {{ $series->pivot->character_name }}
                                            @endif
                                        </p>
                                        <a href="{{ route('site.series.show', $movie->slug) }}"
                                            class="py-2 w-full text-white bg-sky-500 rounded-lg transition-all duration-300 hover:bg-sky-600">
                                            <i class="mr-2 fas fa-play"></i>
                                            <span>مشاهدة</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Add Pagination -->
                    <div class="mt-8 swiper-pagination"></div>

                    <!-- Add Navigation -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
        <script src="{{ asset('assets-site/js/actor.js') }}"></script>
        <script>
            // const contents = [];
        </script>
    @endpush
</x-front-layout>
