@extends('controls::manager.body')

@section('content')

    @if(Route::is('admin.controls.pay.index'))
        <grid:controls::paygrid />
    @endif

    @if(Route::is('admin.controls.storage.index'))
        <grid:controls::storagegrid />
    @endif

    @if(Route::is('admin.controls.index'))
        <grid:controls::controlsgrid />
    @endif

    @if(Route::is('admin.controls.all.index'))
        <grid:controls::controlsallgrid />
    @endif

@endsection

<script src="{{ asset('usr/modules/controls/Resources/Assets/js/function.js') }}"></script>
