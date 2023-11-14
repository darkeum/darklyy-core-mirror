@extends('price::manager.body')

@section('title', __('price::types.title.edit', ['numb' => $data->id]))

@section('content')

    <div id="content" class="col-12">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">@lang('price::types.info')</h4>
                <div class="alert-body">
                    {{ session('status') }}
                </div>
            </div>
        @endif
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">@lang('price::types.course')</h5>
                <form id="admin-form" action="{{ route('admin.price.types.update') }}" method="POST" class="form-horizontal parsley-form">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="id" value="{{ $data->id }}">
                    <div class="mb-2">
                        <label for="typeSelect" class="form-label">@lang('price::add.type')</label>
                        <input value="{{ $data->course }}" type="number" step="0.0001" name="course" class="form-control" placeholder="@lang('price::typea.number.placeholder')" required />
                    </div>
                    @if ($data->link != null)
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" value="1" name="update_link" type="checkbox" id="flexSwitchCheckChecked" checked="">
                            <label class="form-check-label" for="flexSwitchCheckChecked">@lang('price::types.type.price.edit')</label>
                        </div>
                    @endif
                    <div class="mb-2">
                        <input type="submit" class="btn btn-success btn-parsley" id="sub" value="@lang('price::types.btn.edit')" />
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('/js/modules/price/price_type.js') }}"></script>
@endpush