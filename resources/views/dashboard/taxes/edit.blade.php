<x-dashboard-layout>
    <form action="{{route('dashboard.taxes.update',$tax->id)}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include("dashboard.taxes._form")
    </form>
</x-dashboard-layout>
