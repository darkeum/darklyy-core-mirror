@isset($pageConfigs) {!! Helper::updatePageConfig($pageConfigs) !!} @endisset
@php $configData = Helper::applClasses(); @endphp

<!DOCTYPE html>
<html class="loading {{ $configData['theme'] === 'light' ? '' : $configData['layoutTheme'] }} {{ check_company(true) }}"
      lang="@if(session()->has('locale')){{ session()->get('locale') }}@else{{ $configData['defaultLanguage'] }}@endif"
      data-textdirection="{{ env('MIX_CONTENT_DIRECTION') === 'rtl' ? 'rtl' : 'ltr' }}" @if($configData['theme'] === 'dark'){{ 'data-layout="dark-layout"' }}@endif>

{{-- START: Head--}}
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@lang('app.site_desc')">
    <meta name="keywords" content="@lang('app.site_keywords')">
    <meta name="author" content="@lang('app.site_author')">

    <title>
        @hasSection('title') @yield('title') - @endif
        @lang('app.corp') @lang('app.name_' . check_company(true))
    </title>

    <link rel="apple-touch-icon" href="{{ asset('images/ico/apple-icon'. check_company(true, '-') .'.png') }}">
    <link rel="shortcut icon" type="image/x-icon"
          href="{{ asset('images/ico/favicon'. check_company(true, '-') .'.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600"
          rel="stylesheet"/>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/2.3.0/alpine.js"></script>

    {{-- feather icons --}} {{-- Не полный пак иконок... --}}
    <link rel="stylesheet" href="{{ asset(mix('fonts/feather/iconfont.css')) }}">
    {{--/ feather icons --}}

    {{-- tabler icons --}}
    {{--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">--}}
    {{--/ tabler icons --}}

    @include('panels/styles')

    {{-- stack's - Стаки для push --}}
    @stack('head')
    @stack('assets')
    @stack('styles')
    {{-- stack's - Стаки для push --}}

    @livewireStyles
</head>
{{-- END: Head--}}

{{-- BEGIN: Body--}}
@isset($configData['mainLayoutType'])
    @extends($configData['mainLayoutType'] === 'horizontal' ? 'layouts.horizontalLayoutMaster' : 'layouts.verticalLayoutMaster')
@endisset

</html>