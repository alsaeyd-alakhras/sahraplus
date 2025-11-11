<x-dashboard-layout>
    <form action="{{route('dashboard.countries.store')}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @include("dashboard.countries._form")
    </form>
</x-dashboard-layout>
