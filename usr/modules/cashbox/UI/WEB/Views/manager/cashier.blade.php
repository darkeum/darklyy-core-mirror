@extends('cashbox::manager.body')

@section('title', __('cashbox::general.cashier'))

@section('content')
    @grid('cashbox::cashier')
@endsection
