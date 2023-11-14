@extends('scada::manager.body')

@section('title', __('scada::general.dashboard'))

@section('content')
    <grid:scada::indexgrid />
@endsection
