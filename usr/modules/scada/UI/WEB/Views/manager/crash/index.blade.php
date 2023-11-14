@extends('scada::manager.body')

@section('title', __('scada::general.crash'))

@section('content')
    <grid:scada::scadacrashgrid />
@endsection
