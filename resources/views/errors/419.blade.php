@include('layouts.partials.dashboard.head', ['title' => Config::get('app.name', 'دار اليتيم الفلسطيني')])
<div class="wrapper vh-100">
    <div class="mx-auto align-items-center h-100 d-flex w-50">
        <div class="mx-auto text-center">
            <h1 class="m-0 display-1 font-weight-bolder text-danger" style="font-size:80px;">419</h1>
            <h1 class="mb-1 text-muted font-weight-bold">OOPS!</h1>
            <h4 class="mb-3 text-black">حدث خطأ بسيط يرجى العودة وإعادة المحاولة</h4>
            <a href="{{ route('dashboard.home')}}" class="px-5 btn btn-lg btn-primary">العودة للصفحة الرئيسية</a>
        </div>
    </div>
</div>
@include('layouts.partials.dashboard.footer')
