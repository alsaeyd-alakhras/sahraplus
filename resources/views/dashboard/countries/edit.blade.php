<x-dashboard-layout>
    <form action="{{route('dashboard.countries.update',$country->id)}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include("dashboard.countries._form")
    </form>
</x-dashboard-layout>
