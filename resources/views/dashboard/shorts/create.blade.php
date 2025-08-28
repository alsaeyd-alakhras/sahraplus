<x-dashboard-layout>
    <form action="{{route('dashboard.shorts.store')}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @include("dashboard.shorts._form")
    </form>
</x-dashboard-layout>
