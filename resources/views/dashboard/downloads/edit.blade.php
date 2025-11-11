<x-dashboard-layout>
    <form action="{{route('dashboard.downloads.update',$download->id)}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include("dashboard.downloads._form")
    </form>
</x-dashboard-layout>
