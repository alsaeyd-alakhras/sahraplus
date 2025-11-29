<x-dashboard-layout>
    <form action="{{route('dashboard.channel-programs.store')}}" method="post" class="col-12"
        enctype="multipart/form-data">
        @csrf
        @include("dashboard.channel-programs._form")
    </form>
</x-dashboard-layout>
