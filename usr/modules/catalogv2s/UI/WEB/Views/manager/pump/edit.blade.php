@extends('layouts.base')

@section('title', __('catalogv2s::general.pump.edit'))

@section('header')

@endsection

@section('content')

   
    @include('catalogv2s::manager.header')
    
        <section id="multiple-column-form">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Насос {{$unit->title}}</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{route('admin.catalogv2s.pump.update',$unit->id)}}" method="post">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label class="form-label" for="first-name-column">Тип установки</label>
                                                <select name="type_of_instalation" class="form-select form-select-lg" id="selectLarge">
                                                    <option value="0" @if($unit->type_of_instalation==0) selected @endif>Отсутствует</option>
                                                    <option value="1" @if($unit->type_of_instalation==1) selected @endif>Сдвоенный насос Главный-Пиковый</option>
                                                    <option value="2" @if($unit->type_of_instalation==2) selected @endif>Сдвоенный насос Главный-Резервный</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label class="form-label" for="last-name-column">Рабочая линия</label>
                                                <select name="work_line" class="form-select form-select-lg" id="selectLarge">
                                                    <option value="1" @if($unit->work_line==1) selected @endif>1</option>
                                                    <option value="2" @if($unit->work_line==2) selected @endif>2</option>
                                                    <option value="3" @if($unit->work_line==3) selected @endif>3</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label class="form-label" for="city-column">Тип графика</label>
                                                <input type="text" id="city-column" class="form-control" name="type" value="{{$unit->type}}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="mb-1">
                                            <label class="form-label" for="exampleFormControlTextarea1">Точки</label>
                                            <textarea name="data" class="form-control" id="exampleFormControlTextarea1" rows="8">{{$unit->data}}</textarea>
                                        </div>
                                    </div>
                                </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary me-1 waves-effect waves-float waves-light">Сохранить</button>
                                    </div>
                                </form>                           
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @include('catalogv2s::manager.pump.modalAdd')
@endsection
