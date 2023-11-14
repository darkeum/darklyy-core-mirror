@extends('orgs::manager.body')

@section('title', __('orgs::general.manager'))

@section('content')
    <grid:orgs::orgsgrid />
@endsection
