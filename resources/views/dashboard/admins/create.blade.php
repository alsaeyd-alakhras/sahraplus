<x-dashboard-layout>
    <form action="{{route('dashboard.admins.store')}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @include("dashboard.admins._form")
    </form>
</x-dashboard-layout>
