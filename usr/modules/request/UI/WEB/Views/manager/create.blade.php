@extends('layouts.base')

@section('title', __('request::general.add'))
@section('content')
    @include('request::manager.header')
    <section id="content" class="animated fadeIn">
        <div class="row">
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-body">
                            <form class="form-vertical form" action="/administration/module/request/creating" method="POST">
                                <div class="col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="title">@lang('request::general.title')</label>
                                        <input type="text" id="title" class="form-control" name="title"
                                            placeholder="Введите заголовок" required />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="title">@lang('request::general.text')</label>
                                        <input type="text"id="request" name="request" class="form-control"
                                            placeholder="Введите текст заявки" required />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="title">@lang('request::general.obj')</label>
                                        
											{!!$select_obj['select']!!}
                                        
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="title">@lang('request::general.type')</label>
                                        <select id="type" name="type" class="form-control">
                                            <option value="1">Свободная</option>
                                            <option value="2">Атри-проект</option>
                                            <option value="3">Расчет теплообменника</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-9">
                                        <button type="submit" class="btn btn-primary me-1">@lang('request::general.create')</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/forms/select/i18n/ru.js') }}"></script>
@endsection

@section('page-script')
	<script >
		
		{!! $select_obj['js'] !!}
	</script>


@endsection
