<x-dashboard-layout>
    <form action="{{route('dashboard.plan_access.store')}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @include("dashboard.plan_access._form")
    </form>
</x-dashboard-layout>
