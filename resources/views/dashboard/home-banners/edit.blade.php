<x-dashboard-layout>
    <div class="container-fluid">
        <div class="mb-3 row">
            <div class="col-12">
                <h2 class="mb-2 page-title">{{ __('admin.EditBanner') }}</h2>
            </div>
        </div>

        <form action="{{ route('dashboard.home-banners.update', $homeBanner->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('dashboard.home-banners._form')
        </form>
    </div>
</x-dashboard-layout>

