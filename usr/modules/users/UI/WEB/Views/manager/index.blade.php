@extends('users::manager.body')

@section('title', __('users::lang.admin.list'))

@section('content')
    @grid('users::users')
@endsection
