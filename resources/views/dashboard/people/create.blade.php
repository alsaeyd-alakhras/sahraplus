<x-dashboard-layout>
    <form action="{{route('dashboard.people.store')}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @include("dashboard.people._form")
    </form>
</x-dashboard-layout>
