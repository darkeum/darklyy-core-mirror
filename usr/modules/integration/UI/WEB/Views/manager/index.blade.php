@extends('integration::manager.body')

@section('title', __('integration::general.index'))

@section('content')
    <grid:integration::integrationcatalog />
@endsection
