@extends('orgs::manager.body')

@if($unit ?? false)
    @section('title', __('orgs::general.edit'))
@else
    @section('title', __('orgs::general.create'))
@endif

@section('content')

    <div class="card col-12">
        <div class="card-header">
            <h4 class="card-title">
                @if($unit ?? false)
                    Запись №{{ $unit->id }} - {{ ($orgs ?? false) ? $orgs->find($unit->oid)->name : '' }}
                @else
                    Новая организация
                @endif
            </h4>
        </div>
        <div class="card-body">
            @include('orgs::manager.layouts.form')
        </div>
    </div>

@endsection
