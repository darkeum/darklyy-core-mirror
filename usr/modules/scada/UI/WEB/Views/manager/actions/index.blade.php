@extends('scada::manager.body')

@section('title', __('scada::general.actions'))

@section('content')
    <grid:scada::scadaactionsgrid />
@endsection
