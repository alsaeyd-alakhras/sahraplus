<x-dashboard-layout>
    <form action="{{route('dashboard.admins.update',Auth::user()->id)}}" method="post" class="col-12" enctype="multipart/form-data">
        @csrf
        @method('put')
        <input type="hidden" name="settings_profile" value="true">
        @include("dashboard.admins._form",['settings_profile' => $settings_profile])
    </form>
</x-dashboard-layout>
