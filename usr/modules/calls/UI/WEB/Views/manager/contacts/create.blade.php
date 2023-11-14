@extends('calls::manager.body')

@section('title', __('calls::lang.admin.contacts'))

@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"> @if($userPhone ?? false) Изменение телефона @else Добавление телефона @endif</h4>
                    </div>
                    <div class="card-body">
                        @include('calls::manager.contacts.layouts.form')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('vendors/js/forms/cleave/cleave.min.js') }}"></script>
    <script src="{{ asset('vendors/js/forms/cleave/addons/cleave-phone.ru.js') }}"></script>
    <script src="{{asset(mix('vendors/js/forms/select/select2.full.min.js'))}}"></script>


    <script>
        let cleave = new Cleave('.phone-number-mask', {
            phone: true,
            phoneRegionCode: 'RU',
            delimiter: '',
        })
    </script>

    <script>
        let setts = {
            width: '100%',
            dropdownAutoWidth: true,
            language: {
                "noResults": function () {
                    return "Нет результатов";
                }
            }
        }
        $(document).ready(function () {
            $('.select2').select2(setts);
        })
    </script>
@endpush

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection