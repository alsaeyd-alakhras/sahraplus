<x-dashboard-layout>
    <form action="{{ route('dashboard.series.update', $series->id) }}" method="post" class="col-12"
        enctype="multipart/form-data">
        @csrf
        @method('put')
        @include('dashboard.series._form')
    </form>

</x-dashboard-layout>
