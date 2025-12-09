<x-dashboard-layout>
    <form action="{{route('dashboard.coupons.store')}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @include("dashboard.coupons._form")
    </form>
</x-dashboard-layout>
