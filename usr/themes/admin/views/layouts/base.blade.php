@extends('app')
@section('body')
    <header id="topbar" class="text-center bg-white alt ph10 br-b-ddd">
        <div>
            <p class="lead"> @yield('header_title')</p>
            @yield('header_nav')
        </div>
    </header>
    @yield('content')
@endsection