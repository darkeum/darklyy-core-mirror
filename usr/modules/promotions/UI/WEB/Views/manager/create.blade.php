@extends('promotions::manager.body')

@if(isset($unit))
	@section('title', __('promotions::general.edit.title', ['numb' => $unit->id]))
@else
	@section('title', __('promotions::general.create'))
@endif

@section('content')

	<div class="card col-12">
		<div class="card-header">
		  <h4 class="card-title">@if(isset($unit))@lang('promotions::general.edit') @else @lang('promotions::general.create') @endif</h4>
		</div>
		<div class="card-body">
			@include('promotions::manager.layouts.form')
		</div>
	  </div>
    
@endsection
