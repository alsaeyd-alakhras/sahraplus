<x-dashboard-layout>
    <form action="{{route('dashboard.people.update',$person->id)}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include("dashboard.people._form")
    </form>
</x-dashboard-layout>
