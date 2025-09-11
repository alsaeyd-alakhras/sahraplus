<x-dashboard-layout>
    <form action="{{route('dashboard.episodes.update',$episode->id)}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include("dashboard.series.episodes._form")
    </form>
</x-dashboard-layout>
