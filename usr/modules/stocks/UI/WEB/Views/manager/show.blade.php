@extends('stocks::manager.body')

@section('title', __('stocks::general.stock.title.show', ['numb' => $unit->id]))

@section('content')

    <div class="card col-12">
        <div class="card-header">
            <h4 class="card-title">@lang('stocks::general.stock.title.show', ['numb' => $unit->id])</h4>
        </div>
        <div class="card-body">
            <div class="row">

                {{-- Заголовок --}}
                <div class="col-6">
                    <div class="mb-0">
                        <strong>@lang('stocks::general.stock.name'):</strong>
                    </div>
                    <div class="mb-1">
                        {{ $unit->name }}
                    </div>
                </div>
                {{-- Заголовок --}}

                {{-- Организация --}}
                <div class="col-6">
                    <div class="mb-0">
                        <strong>@lang('stocks::general.org'):</strong>
                    </div>
                    <div class="mb-1">
                        {{ $org->name }}
                    </div>
                </div>
                {{-- Организация --}}
            </div>
        </div>
        <div class='card-footer'>
            <a href="{{ route('admin.stocks.edit', $unit->id) }}" class="btn btn-outline-secondary waves-effect">@lang('stocks::general.edit')</a>
        </div>
    </div>

@endsection
