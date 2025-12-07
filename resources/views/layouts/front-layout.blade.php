@php
    $locale = $lang ?? app()->getLocale();
    $siteName = $locale === 'ar'
        ? ($settings['site_name_ar'] ?? config('settings.app_name'))
        : ($settings['site_name_en'] ?? config('settings.app_name'));
    $pageTitle = $title ?: $siteName;
@endphp

@include('layouts.partials.front.head', ['title' => $pageTitle, 'lang' => $locale])

@include('layouts.partials.front.nav')

{{ $slot }}

@include('layouts.partials.front.footer')
@include('layouts.partials.front.modal')
@include('layouts.partials.front.end')