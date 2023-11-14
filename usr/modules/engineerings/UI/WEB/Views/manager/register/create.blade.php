
@section('title', __('engineerings::general.register.grid'))

@extends('engineerings::manager.body')

@section('content')

    <div class = "row">
        <div class="col-md-12 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Данные</h4>
                </div>
                <div class="card-body">
                    <form class="form form-vertical" action="{{route('admin.engineerings.register.creating')}}" method="post">
                        @if($id)
                        
                            <input type="hidden" value="{{$id}}" name="id" id="mainId">

                        @endif
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="first-name-vertical">Шифр</label>
                                    <input type="text" class="form-control" id="numb" name="numb" placeholder="Введите уникальный шифр" value="{{$numb}}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="email-id-vertical">Раздел</label>
                                    <div class="col-xs-{{$input_size}}">
                                        <select class="col-12 select2" id="section" name="section[]" multiple required>
                                                @foreach($section_type as $key => $val)
                                                    <option value='{{$key}}' @if(in_array($key, $section)) selected @endif>{{$val}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="contact-info-vertical">Объект</label>
                                    {!!$select_obj['select']!!}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="date">Дата</label>
                                    <input type="date" id="date" name="date"
                                            class="form-control flatpickr"
                                            value="{{ $date }}"
                                            placeholder="Дата" required/>
                                </div>
                            </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="contact-info-vertical">Исполнитель</label>
                                    {!!$select_usr['select']!!}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="contact-info-vertical">Статус</label>
                                    <select class="form-control select2" id="status" name="status" required>
                                            @foreach($status_type as $key => $val)
                                                <option value='{{$key}}' @if($key === $status) selected @endif>{{$val}}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="{{ route('admin.engineerings.register') }}" class="btn btn-secondary">Отмена</a>         
                            <button type="submit" class="btn btn-primary">@if(isset($id)) Сохранить @else Создать @endif</button>
                        </div>
                    </form>
                </div>
            </div>
            
            @if($id)
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Файлы</h4>
                        </div>
                        <div class="card-body">
                            

                        </div>
                    </div>
                </div>
            @endif
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
