@extends('stocks::manager.body')

@section('title', __('stocks::general.index'))

@section('content')   
    <grid:stocks::stocksgrid />
@endsection