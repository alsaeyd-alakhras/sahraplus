<x-dashboard-layout>
    <form action="{{route('dashboard.movies.update',$movie->id)}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include("dashboard.movies._form")
    </form>
        @push('scripts')
        <script>
            var form_type = "edit";
        </script>
    @endpush
</x-dashboard-layout>
