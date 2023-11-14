@extends('price::manager.body')

@section('title', __('price::add.title'))

@section('content')

    <div id="content" class="col-12">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">@lang('price::add.info')</h4>
                <div class="alert-body">
                    {{ session('status') }}
                </div>
            </div>
        @endif
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">@lang('price::add.title')</h5>
                <form id="admin-form" action="{{ route('admin.price.save') }}" method="post" name="" role="form" class="form-horizontal parsley-form">
                    <div class="mb-2">
                        <label for="typeSelect" class="form-label">@lang('price::add.type')</label>
                        <select class="form-select" name="price_type" id="typeSelect" aria-label="@lang('price::add.type')">
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}">{{ $type->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" name="open" type="checkbox" id="flexSwitchCheckChecked" checked="">
                        <label class="form-check-label" for="flexSwitchCheckChecked">@lang('price::add.open')</label>
                    </div>
                    <div class="mb-2">
                        <input type="submit" class="btn btn-success btn-parsley" id="sub" value="@lang('price::add.add')" />
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
