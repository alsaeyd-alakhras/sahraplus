<x-dashboard-layout>
    <form action="{{ route('dashboard.movies.store') }}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @include('dashboard.movies._form')
    </form>

    @push('scripts')
        <script>
            var form_type = "create";
        </script>
    @endpush
</x-dashboard-layout>
