@extends('changelog::manager.body')

@section('title', __('changelog::lang.module'))

@section('content')
    <grid:changelog::changelogindex/>
@endsection

