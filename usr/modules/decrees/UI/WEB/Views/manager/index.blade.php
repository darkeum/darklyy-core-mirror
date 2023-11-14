@extends('decrees::manager.body')

@section('title', __('decrees::admin.reestr'))

@section('content')
    <grid:decrees::index />
@endsection
