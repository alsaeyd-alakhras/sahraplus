<x-dashboard-layout>
    <form action="{{route('dashboard.live-tv-channels.store')}}" method="post" class="col-12"
        enctype="multipart/form-data">
        @csrf
        @include("dashboard.live-tv-channels._form")
    </form>
</x-dashboard-layout>