@extends('orgs::manager.body')

@section('title', __('orgs::general.list.sign'))

@section('content')
    <grid:orgs::signsgrid />
@endsection
