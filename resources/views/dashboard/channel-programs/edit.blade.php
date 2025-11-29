<x-dashboard-layout>
    <form action="{{route('dashboard.channel-programs.update', $program->id)}}" method="post" class="col-12"
        enctype="multipart/form-data">
        @csrf
        @method('put')
        @include("dashboard.channel-programs._form")
    </form>
</x-dashboard-layout>
