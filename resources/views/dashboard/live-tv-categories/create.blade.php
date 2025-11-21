<x-dashboard-layout>
    <form action="{{route('dashboard.live-tv-categories.store')}}" method="post" class="col-12"
        enctype="multipart/form-data">
        @csrf
        @include("dashboard.live-tv-categories._form")
    </form>
</x-dashboard-layout>