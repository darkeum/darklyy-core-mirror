@extends('cashbox::manager.body')

@section('title', __('cashbox::general.checks'))

@section('content')
    @grid('cashbox::checks')
@endsection
