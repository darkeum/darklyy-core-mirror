@extends('cashbox::manager.body')

@section('title', __('cashbox::general.index'))

@section('content')
    @grid('cashbox::cashbox')
@endsection
