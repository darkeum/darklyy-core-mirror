@extends('layouts.base')

{{-- @section('title', __('engineerings::general.register.grid')) --}}

@section('header')

@endsection

@section('content')

@include('verifications::manager.header')

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Файлы</h4>
    </div>
    <div class="card-body">
        

    </div>
</div>

@endsection

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
    <!-- Page css files -->
    {{--<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">--}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/forms/select/i18n/ru.js') }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/dist/l10n/ru.js') }}"></script>
@endsection

@section('page-script')
	<script >
            flatpickr.localize(flatpickr.l10ns.{!! app()->getLocale() !!});
                $('.flatpickr').flatpickr({
                    altInput: true,
                    altFormat: "d.m.Y",
                    // altFormat: "j F Y",
                    // allowInput: true,
                    locale: "{!! app()->getLocale() !!}"
            });



		{!! $select_obj['js'] !!}
        {!! $select_usr['js'] !!}
	</script>


@endsection



@push('scripts')
    <script>
        $(document).ready(function() {
            $('#section').select2({
                placeholder: "Выберите раздел",
            });
        });
    </script>
@endpush
