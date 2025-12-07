<x-dashboard-layout>
    <form action="{{route('dashboard.sub_plans.store')}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @include('dashboard.subscription_plans._form')
    </form>
</x-dashboard-layout>
