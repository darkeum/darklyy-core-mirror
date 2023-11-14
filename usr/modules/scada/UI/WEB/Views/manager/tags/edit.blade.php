@extends('scada::manager.body')

@section('title', __('scada::form.main.edit.title', ['numb' => $item->id]))

@section('content')
    @if (session()->has('updates'))
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">@lang('scada::form.info')</h4>
            <div class="alert-body">
                {{ session('updates') }}
            </div>
        </div>
    @endif
    @include('scada::manager.tags.form')
@endsection
