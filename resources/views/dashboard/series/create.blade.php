<x-dashboard-layout>
    <form action="{{ route('dashboard.series.store') }}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @include('dashboard.series._form')
    </form>

</x-dashboard-layout>
