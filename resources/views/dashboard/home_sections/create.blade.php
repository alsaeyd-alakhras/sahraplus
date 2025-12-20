<x-dashboard-layout>
    <form action="{{route('dashboard.home_sections.store')}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @include('dashboard.home_sections._form')
    </form>
</x-dashboard-layout>

