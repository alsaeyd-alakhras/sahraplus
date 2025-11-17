<x-front-layout>
    @php
        $name = 'name_' . app()->getLocale();
    @endphp

    <!-- صفحة التصنيفات -->
    <div class="px-4 pt-[6rem] text-white max-w-[95%] mx-auto">
        <h2 class="mb-6 text-3xl font-bold text-right">التصنيفات</h2>

        <div id="categoriesGrid" class="grid grid-cols-2 gap-6 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 group">

            @foreach ($categories as $category)
                <a href="{{ route('site.categories.show', $category->slug) }}"
                    class="category-item flex flex-col items-center transition-all duration-300 transform group-hover:opacity-70 hover:!opacity-100 hover:scale-110 cursor-pointer">
                    @if ($category->image_full_url)
                        <img src="{{ $category->image_full_url }}" 
                            alt="{{ $category->$name }}" 
                            class="object-contain w-64 h-64">
                    @else
                        <div class="flex items-center justify-center w-64 h-64 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl">
                            <span class="text-2xl font-bold text-white">{{ $category->$name }}</span>
                        </div>
                    @endif
                </a>
            @endforeach

        </div>
    </div>


    @push('scripts')
        <script>
            // const contents = [];
        </script>
    @endpush
</x-front-layout>

