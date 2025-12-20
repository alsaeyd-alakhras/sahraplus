<x-dashboard-layout>
    <form action="{{route('dashboard.home_sections.update',$section->id)}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include('dashboard.home_sections._form')
    </form>
</x-dashboard-layout>

