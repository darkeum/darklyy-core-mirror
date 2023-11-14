@extends('promotions::manager.body')

@section('title', __('promotions::general.manager'))

@lang("promotions::general.manager")

@section('content')
   
    <grid:promotions::indexgrid />
    
@endsection