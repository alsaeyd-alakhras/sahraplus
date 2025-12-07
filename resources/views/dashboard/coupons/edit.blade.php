<x-dashboard-layout>
    <form action="{{route('dashboard.coupons.update',$coupon->id)}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include("dashboard.coupons._form")
    </form>
</x-dashboard-layout>
