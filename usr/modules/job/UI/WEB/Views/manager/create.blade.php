@extends('job::manager.body')

@isset($unit)
    @section('title', __('job::general.edit'))
@else
    @section('title', __('job::general.create'))
@endisset


@section('content')
    <form action="@isset($unit) {{ route('admin.job.update',$unit) }} @else {{ route('admin.job.store') }} @endif" method="POST">
        @isset($unit)
            @method('PUT') 
        @endisset
        <div class="card">
            <div class="card-header"> @isset($unit) <h3>Редактирование вакансии №{{$unit->id}}</h3> @else <h3>Создание вакансии</h3> @endif</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="row">

                            <div class="col-6">
                                <div class="mb-1">
                                    <label class="form-label" for="category">Отдел</label>
                                    <select name="category" id="category" class="form-control select2" required>
                                        @foreach ($categories as $item)
                                            <option @selected(isset($unit) && $item['id'] == $unit->category) value="{{$item['id']}}">{{ $item['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-1">
                                    <label class="form-label" for="responsible">Руководитель</label>
                                    <select name="responsible" id="responsible" class="form-control select2" required>
                                        @foreach ($usersList as $item)
                                            <option @selected(isset($unit) && $item->id == $unit->uid) value="{{$item->id}}">{{ shortUserName($item->id) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-8">
                                <div class="mb-1">
                                    <label class="form-label" for="title">Название вакансии</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i data-feather='pen-tool'></i></span>
                                        <input required id="title" type="text" name="title" value="@isset($unit){{$unit->title}}@endisset" placeholder="Укажите название вакансии" class="form-control">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-4">
                                <div class="mb-1">
                                    <label class="form-label" for="salary">Информация о зарплате</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="fa-solid fa-ruble-sign"></i></span>
                                        <input id="salary" type="text" name="salary" value="@isset($unit){{$unit->salary}}@endisset" placeholder="Укажите зарплату" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="description">Описание вакансии</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i data-feather='message-circle'></i></span>
                                        <textarea name="description" id="description" placeholder="Укажите описание вакансии" class="form-control">@isset($unit){{$unit->description}}@endisset</textarea>
                                    </div>
                                </div>
                            </div>    

                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-1">
                                    <label class="form-label" for="conditions">Список условий</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i data-feather='list'></i></span>
                                        <textarea name="conditions" id="conditions" placeholder="Укажите список условий" class="form-control">@isset($unit){{$conditions}}@endisset</textarea>
                                    </div>
                                    <small class="text-info m-0 p-0"><strong class="text-warning">Внимание: </strong>каждое условие должно начининаться с новой строки</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-1">
                                    <label class="form-label" for="duties">Список обязанностей</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i data-feather='list'></i></span>
                                        <textarea name="duties" id="duties" placeholder="Укажите список обязанностей" class="form-control">@isset($unit){{$duties}}@endisset</textarea>
                                    </div>
                                    <small class="text-info m-0 p-0"><strong class="text-warning">Внимание: </strong>каждая обязанность должна начининаться с новой строки</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="requirements">Список требований</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i data-feather='list'></i></span>
                                        <textarea name="requirements" id="requirements" placeholder="Укажите список требований" class="form-control">@isset($unit){{$requirements}}@endisset</textarea>
                                    </div>
                                    <small class="text-info m-0 p-0"><strong class="text-warning">Внимание: </strong>каждое требование должно начининаться с новой строки</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-between">
                <button class="btn btn-danger">Отмена</button>
                <button class="btn btn-success">@isset($unit)Изменить @else Создать @endisset</button>
            </div>
        </div>
    </form>
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">

@endpush

@push('scripts')
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
    <script>
         $(".select2").select2({
            placeholder: 'Нажмите чтобы выбрать',
            width: '100%',
            escapeMarkup : function(markup) {
                return markup;
            }
        });
        @if(session()->has('message'))
            Swal.fire({
                icon: 'success',
                title: 'Отлично!',
                html: "{{ session()->get('message') }}",
                timer: 2000,
                timerProgressBar: true,
            });
        @endif
    </script>
@endpush

