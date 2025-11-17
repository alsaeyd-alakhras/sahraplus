<x-dashboard-layout>
    <form action="{{route('dashboard.movie-categories.update',$movie_category->id)}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include('dashboard.categories._form')
    </form>
</x-dashboard-layout>
