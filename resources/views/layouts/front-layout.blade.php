@include('layouts.partials.front.head', ['title' => $title])

@include('layouts.partials.front.nav')

{{ $slot }}

@include('layouts.partials.front.footer')
@include('layouts.partials.front.modal')
@include('layouts.partials.front.end')
[]
