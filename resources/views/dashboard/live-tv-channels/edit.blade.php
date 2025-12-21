<x-dashboard-layout>
    <form action="{{route('dashboard.live-tv-channels.update', $channel->id)}}" method="post" class="col-12"
        enctype="multipart/form-data">
        @csrf
        @method('put')
        @include("dashboard.live-tv-channels._form")
    </form>
</x-dashboard-layout>