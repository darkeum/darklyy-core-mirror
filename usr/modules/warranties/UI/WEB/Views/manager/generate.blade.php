@extends('warranties::manager.body')

@section('title', __('warranties::general.create'))

@section('content')
    <section id="ajax-datatable-warranties">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <livewire:warranties::warrantysearch />
                    <hr class="my-0" />
                    <div class="card-datatable">
                        <table class="ajax-datatable-warranties table">
                            <thead>
                                <tr class="p-1">
                                    <th class="text-center col-2">@lang('warranties::general.table.id')</th>
                                    <th class="col-7">@lang('warranties::general.table.title')</th>
                                    <th class="text-center col-2">@lang('warranties::general.table.sku')</th>
                                    <th class="text-center col-1">@lang('warranties::general.table.actions')</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/full/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
@endsection

@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset('vendors/js/tables/datatable/full/datatables.min.js') }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/dist/l10n/ru.js') }}"></script>
    <script src="{{ asset('vendors/js/forms/select/i18n/ru.js') }}"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/tables/table-datatables-advanced.js')) }}"></script>
@endsection
