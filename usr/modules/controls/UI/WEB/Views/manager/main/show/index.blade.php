@extends('controls::manager.body')

@section('content')
   @include('controls::manager.main.show.content')
@endsection

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('css/base/pages/app-invoice.css')}}">
    <link rel="stylesheet" href="{{asset('css/base/pages/app-invoice-fix.css')}}">
    <link rel="stylesheet" href="{{ asset('usr/modules/controls/Resources/Assets/css/Style.css') }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('vendor-script')
    <link rel="stylesheet" href="{{ asset('usr/modules/controls/Resources/Assets/css/Style.css') }}">
    <script src="{{ asset('usr/other/sub/load_window/asset/js/load_window.js') }}"></script>
    <script src="{{ asset('usr/modules/controls/Resources/Assets/js/function.js') }}"></script>
    <script src="{{ asset('usr/modules/controls/Resources/Assets/js/show.js') }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
@endsection


@section('page-script')
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/dist/l10n/ru.js') }}"></script>

    <script>
        flatpickr.localize(flatpickr.l10ns.{!! app()->getLocale() !!});
        $('.flatpickr').flatpickr({
            altInput: true,
            altFormat: "d.m.Y",
            locale: "{!! app()->getLocale() !!}"
        });

        $('#plan_pay_date').change(event => {
            let value = event.target.value
            if(value) {
                $.post('{{ route('admin.controls.updateDatePay', $control->id) }}', {plan_pay_date: value})
            }
        })
    </script>
@endsection
