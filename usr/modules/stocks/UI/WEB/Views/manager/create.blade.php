@extends('stocks::manager.body')

@if(isset($unit))
	@section('title', __('stocks::general.stock.title.edit', ['numb' => $unit->id]))
@else
	@section('title', __('stocks::general.create'))
@endif

@section('content')

	<div class="card col-12">
		<div class="card-header">
		  <h4 class="card-title">@if(isset($unit))@lang('stocks::general.edit') @else @lang('stocks::general.stock.create') @endif</h4>
		</div>
		<div class="card-body">
			@include('stocks::manager.layouts.form')
		</div>
	  </div>
    
@endsection
