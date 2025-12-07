<x-dashboard-layout>
    <form action="{{route('dashboard.taxes.store')}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @include("dashboard.taxes._form")
    </form>
</x-dashboard-layout>
