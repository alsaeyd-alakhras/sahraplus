<x-dashboard-layout>
    <form action="{{route('dashboard.episodes.store')}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @include("dashboard.series.episodes._form")
    </form>
</x-dashboard-layout>
