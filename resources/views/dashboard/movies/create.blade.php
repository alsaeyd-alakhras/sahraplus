<x-dashboard-layout>
    <form action="{{ route('dashboard.movies.store') }}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @include('dashboard.movies._form')
    </form>
</x-dashboard-layout>
