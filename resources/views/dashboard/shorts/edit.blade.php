<x-dashboard-layout>
    <form action="{{route('dashboard.shorts.update',$short->id)}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include("dashboard.shorts._form")
    </form>
</x-dashboard-layout>
