<x-front-layout>
    <div class="container px-6 py-10 mx-auto max-w-[95%]">
        <h1 class="mb-8 text-3xl font-bold text-white">المفضلة</h1>

        <!-- Loading State -->
        <div id="favorites-loading" class="flex justify-center items-center py-20">
            <div class="text-center">
                <div class="mx-auto mb-4 w-12 h-12 rounded-full border-b-2 border-white animate-spin"></div>
                <p class="text-white">جاري تحميل المفضلة...</p>
            </div>
        </div>

        <!-- Empty State -->
        <div id="favorites-empty" class="hidden py-20 text-center">
            <i class="fas fa-heart text-6xl text-gray-600 mb-4"></i>
            <h2 class="text-2xl font-bold text-white mb-2">لا توجد مفضلات</h2>
            <p class="text-gray-400 mb-6">لم تقم بإضافة أي محتوى إلى المفضلة بعد</p>
            <a href="{{ route('site.movies') }}" class="inline-block px-6 py-3 bg-fire-red text-white rounded-lg hover:bg-red-700 transition-all">
                تصفح الأفلام
            </a>
        </div>

        <!-- Favorites Grid -->
        <div id="favorites-container" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5"></div>
    </div>

    @push('scripts')
    <script src="{{ asset('assets-site/js/user-lists.js') }}"></script>
    @endpush
</x-front-layout>

