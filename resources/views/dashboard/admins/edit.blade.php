<x-dashboard-layout>
    <form action="{{route('dashboard.admins.update',$admin->id)}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include("dashboard.admins._form")
    </form>
</x-dashboard-layout>
