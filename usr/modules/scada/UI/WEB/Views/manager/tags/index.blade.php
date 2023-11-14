@extends('scada::manager.body')

@section('title', __('scada::general.tags'))

@section('content')
    <grid:scada::scadatagsgrid />
@endsection
