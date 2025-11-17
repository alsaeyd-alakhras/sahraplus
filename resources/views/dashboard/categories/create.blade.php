<x-dashboard-layout>
    <form action="{{route('dashboard.movie-categories.store')}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @include('dashboard.categories._form')
    </form>
</x-dashboard-layout>
