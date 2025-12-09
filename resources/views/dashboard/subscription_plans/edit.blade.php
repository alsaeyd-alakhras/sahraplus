@php
    $rates = $rates;
@endphp
<script>
    window.currencyRates = @json($rates);
</script>

<x-dashboard-layout>
    <form action="{{route('dashboard.sub_plans.update',$sub->id)}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include('dashboard.subscription_plans._form')
    </form>
</x-dashboard-layout>
