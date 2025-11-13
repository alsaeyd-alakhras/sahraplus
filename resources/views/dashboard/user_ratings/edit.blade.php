<x-dashboard-layout>
    <form action="{{route('dashboard.userRatings.update',$userRating->id)}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include("dashboard.user_ratings._form")
    </form>
</x-dashboard-layout>
