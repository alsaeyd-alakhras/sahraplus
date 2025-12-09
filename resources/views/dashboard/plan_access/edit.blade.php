<x-dashboard-layout>
    <form action="{{route('dashboard.plan_access.update',$planAccess->id)}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include("dashboard.plan_access._form")
    </form>
</x-dashboard-layout>
