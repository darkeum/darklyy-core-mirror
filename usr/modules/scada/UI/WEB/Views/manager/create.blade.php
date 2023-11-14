@extends('scada::manager.body')

@section('title', __('scada::form.main.create.title'))

@section('content')
    @if (session()->has('created'))
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">@lang('scada::form.info')</h4>
            <div class="alert-body">
                {{ session('created') }}
            </div>
        </div>
    @endif
    @include('scada::manager.form')
@endsection
