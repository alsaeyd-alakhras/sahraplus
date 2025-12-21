<x-dashboard-layout>
    <form action="{{route('dashboard.live-tv-categories.update', $category->id)}}" method="post" class="col-12"
        enctype="multipart/form-data">
        @csrf
        @method('put')
        @include("dashboard.live-tv-categories._form")
    </form>
</x-dashboard-layout>