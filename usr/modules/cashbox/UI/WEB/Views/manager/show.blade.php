@extends('cashbox::manager.body')

@section('title', __('cashbox::main.show', ['numb' => $cashbox->id]))

@push('scripts')
    <script>
        const KkmServerAddIn = {};
        const cashier_shifts = {!! $cashier_shifts !!};
    </script>
    <script src="{{ asset('/js/modules/cashbox/show.js') }}"></script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('/css/modules/cashbox/show.css') }}">
@endpush

@section('content')
    <div id="content" class="col-12">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">@lang('cashbox::main.show', ['numb' => $cashbox->id])</h5>
                <p class="card-text">
                    @lang('cashbox::main.description')
                </p>
                <div id="block1" class="mb-1" style="display: none;">
                    <label class="form-label" for="largeInput">@lang('cashbox::main.cashier')</label>
                    <div class="input-group">
                        <select id="CashierName" class="form-control">
                            @foreach ($cashbox->cashiers as $cashier)
                                <option value="{{ $cashier->name }}" data-inn="{{ $cashier->inn }}" data-id="{{ $cashier->id }}">{{ $cashier->name }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary" type="button" id="button-init">@lang('cashbox::main.cashier.choose')</button>
                    </div>
                    <input type="hidden" id="NumDevice" value="{{ $cashbox->NumDevice }}">
                    <input type="hidden" id="cashbox_id" value="{{ $cashbox->id }}">
                </div>
                <div id="block2" style="display: none;">
                    <div class="mb-1">@lang('cashbox::main.cashier.selected') <b id="cashier-name"></b></div>
                    <div class="d-flex flex-wrap buttons">
                        <button type="button" class="custom-button btn btn-outline-dark" id="open-shift">@lang('cashbox::main.action.open')</button>
                        <button type="button" class="custom-button btn btn-outline-dark" id="close-shift">@lang('cashbox::main.action.close')</button>
                        <button type="button" class="custom-button btn btn-outline-dark" id="x-report">@lang('cashbox::main.action.report')</button>
                        <button type="button" class="custom-button btn btn-danger" id="">@lang('cashbox::main.action.collection')</button>
                        <button type="button" class="custom-button btn btn-danger" id="">@lang('cashbox::main.action.depositing')</button>
                        <button type="button" class="custom-button btn btn-danger" id="">@lang('cashbox::main.action.drawer')</button>
                        <button type="button" class="custom-button btn btn-outline-dark" id="print-ofd">@lang('cashbox::main.action.ofd')</button>
                        <button type="button" class="custom-button btn btn-outline-dark" id="data-fn">@lang('cashbox::main.action.fn')</button>
                        <button type="button" class="custom-button btn btn-outline-dark" id="data-kkt">@lang('cashbox::main.action.kkt')</button>
                    </div>
                    <pre id="main-console" class="mt-3 d-none"></pre>
                </div>
            </div>
        </div>
    </div>
@endsection
