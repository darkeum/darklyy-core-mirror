@extends('layouts.base')

{{-- @section('title', __('request::general.show', ['numb' => ])) --}}


@section('content')
    @include('request::manager.header')
    
    
    <div class="row">
        <div class="col-md-6 col-12" >
            <form id="mainPanel"> 
                <div class="card">
                    <div class="card-header">
                        <h2 class="changeH3">Заявка №{{$id}} - {{$title}}</h2>
                        <div class="takeBtn">
                            @if(empty($uid_exec))
                                <input type="button" class="btn btn-success btn-block" onclick="takeBtn();" value="Взять заявку">
                            @endif
                            
                            @if($status === "2")
                                <div class="takeBtn">
                                    <input type="submit" value="Завершить заявку" class="btn btn-success btn-block" @if($count_query > 0 )  @else disabled @endif>
                                </div>
                            @endif
                            
                            @if($status === "3" && $type === "3")
                                <div class="takeBtn">
                                    <input type="button" value="Редактировать" class="btn btn-warning btn-block" onclick="edit();">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">

                        
                        <table class="table table-borderless">
                            <tr>
                                <td>Статус </td>
                                <td><strong>
                                    @switch($status)
                                        @case(1)
                                            Ожидает исполнителя
                                            @break
                                        @case(2)
                                            Ожидает выполнения
                                            @break
                                        @case(3)
                                            Выполнено
                                            @break
                                            
                                    @endswitch
                                </strong></td>
                        </tr>
                        
                            

                        <tr >
                            <td>Тип заявки</td> <td><strong>{{$title}}</strong></td>
                        <tr>

                        <tr>
                            <td>Наименование у заказчика</td>  <td><strong> @if(empty($title_user)) Не указано @else {{$title_user}} @endif </strong></td>
                        </tr>

                        <tr>
                            <td colspan="2">Постановщик</td>
                        </tr> 
                        <tr >
                            <td><img class="avatar" src="{{avatar($uid_dir)}}" style="width: 30%"></td>                        
                            <td><strong>{{($uid_dir === 0 ? "Гость" : fullUserName($uid_dir))}}</strong></td>
                        </tr>

                        <tr>
                            <td>Исполнитель</td>
                        </tr> 
                        <tr >
                            <td><img class="avatar" src="{{avatar($uid_exec)}}" style="width: 30%"></td>                        
                            <td ><strong>{{fullUserName($uid_exec)}}</strong></td>
                        </tr>

                        </table>

                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6 col-12">

            @if($type == 5)
                <div class="card">
                    <div class="card-header">
                        <h2 class="changeH3">Информация о запросе</h2>
                        <div class="col-12">
                            <div class="row mt-1">
                                    <div class="col-6">
                                        <h5 class="mt-1">Рабочие параметры</h5>
                                        <div class="m-1">
                                            <div class="row">
                                                <div class="col-6">Обозначение рабочей точки</div>
                                                <div class="col-6"> {{$request_data['work_mark']}}</div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-6">Расход, (Q)</div>
                                                <div class="col-6"> {{$request_data['q']}}</div>
                                            </div>
                                            @if(!empty($request_data['q_all']))
                                                <div class="row mt-1">
                                                    <div class="col-6">Общий расход, (ΣQ)</div>
                                                    <div class="col-6"> {{$request_data['q_all']}}</div>
                                                </div>
                                            @endif
                                            <div class="row mt-1">
                                                <div class="col-6">Напор, (H)</div>
                                                <div class="col-6"> {{$request_data['h']}}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <h5 class="mt-1">Исходные данные</h5>
                                        <div class="m-1">
                                            <div class="row">
                                                <div class="col-6">Температура</div>
                                                <div class="col-6"> {{$request_data['t']}}</div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-6">Плотность</div>
                                                <div class="col-6"> {{$request_data['density']}}</div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-6">Вязкость</div>
                                                <div class="col-6"> {{$request_data['viscosity']}}</div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-6">Коэффициент запаса</div>
                                                <div class="col-6"> {{$request_data['coefficient']}}</div>
                                            </div>
                                        </div>
                                    </div>  
                            </div>
                            
                            <div class="row mt-1">

                                <div class="col-6">
                                    <h5 class="mt-1">Тип переключения</h5>
                                    <div class="m-1">
                                        {{-- <div class="row">
                                            <div class="col-6">Обозначение рабочей точки</div>
                                            <div class="col-6"> {{$request_data['work_mark']}}</div>
                                        </div> --}}
                                        <div class="row mt-1">
                                            <div class="col-6">Число насосов</div>
                                            <div class="col-6"> {{$request_data['pump_count']}}</div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-6">Pезервный насос</div>
                                            <div class="col-6"> @if($request_data['pump_reserve']==0) Нет @else Да @endif </div>
                                        </div>
                                    </div>

                                    <h5 class="pt-2">О насосе</h5>
                                    <div class="m-1">
                                        <div class="row mt-1">
                                            <div class="col-6">Модель</div>
                                            @foreach($request_data['model'] as $item)
                                                <div class="col-6"> {{$item}} </div>
                                            @endforeach
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-6">Название насоса</div>
                                            @foreach($manafacture as $item)
                                                <div class="col-6"> {{$item->name}} </div>
                                            @endforeach
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-6">Номер в каталоге</div>
                                                <div class="col-6"> {{$catalog->id}} </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-6">Название в каталоге</div>
                                                <div class="col-6"> {{$catalog->title}} </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-6">Артикул</div>
                                                <div class="col-6"> {{$catalog->sku}} </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-6">Тип насоса</div>
                                            <div class="col-6">
                                                 @switch($request_data['typeBinary'])
                                                
                                                    @case(1)
                                                        Одинарный
                                                        @break
                                                    @case(2)
                                                        Сдвоенный
                                                        @break
                                                
                                                    
                                                @endswitch
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <h5 class="mt-1">Дополнительные данные</h5>
                                    <div class="m-1">
                                        <div class="row mt-1">
                                            <div class="col-6">Назначение оборудования</div>
                                            <div class="col-6">

                                                @switch($request_data['type'])
                                                
                                                @case(1)
                                                    Отопление
                                                    @break
                                                @case(2)
                                                    Циркуляция ГВС
                                                    @break
                                                @case(3)
                                                    Подпитка отопления
                                                    @break
                                                @case(4)
                                                    Повысительный ХВС / ГВС
                                                    @break
                                                @case(5)
                                                    Пожарные насосы
                                                    @break
                                                    
                                                @endswitch
                                                
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-6">Частотный преобразователь</div>
                                            <div class="col-6">
                                                 @switch($request_data['frequency'])
                                                
                                                @case(1)
                                                    С ПЧ
                                                    @break
                                                @case(2)
                                                    Без ПЧ
                                                    @break
                                               
                                                    
                                                @endswitch
                                            </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-6">Частотный преобразователь</div>
                                            <div class="col-6">
                                                 @switch($request_data['voltage'])
                                                
                                                    @case(1)
                                                        1~ 230 V / 50 Hz
                                                        @break
                                                    @case(2)
                                                        3~ 400 V / 50 Hz
                                                        @break
                                               
                                                    
                                                @endswitch
                                            </div>
                                        </div>


                                        <div class="row mt-1">
                                            <div class="col-6">Критерий поиска</div>
                                            <div class="col-6">
                                                 @switch($request_data['pumpSelector'])
                                                
                                                    @case(0)
                                                        По назначению
                                                        @break
                                                    @case(1)
                                                        По модели
                                                        @break
                                                    @case(2)
                                                        Прямой выбор
                                                        @break
                                               
                                                    
                                                @endswitch
                                            </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-6">Объект</div>
                                            @if($obj != "Отсутствует")
                                                @foreach($obj as $item)
                                                    <div class="col-6"> {{$item->name}} </div>
                                                @endforeach
                                            @else
                                                <div class="col-6"> {{$obj}}</div>
                                            @endif
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-6">Организация</div>                                           
                                                <div class="col-6"> {{$org}} </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-6">Исключить из поиска не подходящие насосы</div>                                           
                                                <div class="col-6"> @if($request_data['pump_switch'] == 0) Нет @else Да @endif </div>
                                        </div>


                                    </div>
                                </div>


                            </div>
                            
       
                        </div>

                       
                    </div>    

                </div>
            @endif

            @if($type == 6)
                <livewire:request::add :station_data="$station" :electric_data="$electric" :request_id="$id"/>
            @endif
            

          

            <div class="card">
                <div class="card-header">
                    <h2 class="changeH3">Приложенные файлы</h2>
                    <div class="col-12">
                        <div class="mt-2 mb-2">
                            
                            @if(count($files)!=0)
                                <ul>
                                    @foreach ($files as $file)
                                        <li><a href="/download/{{$file->id}}">{{$file->name}}</a></li>
                                    @endforeach
                                </ul>

                            @else
                                Отсутствуют
                            @endif
                       
                        </div>
                    </div>
                 
                </div>
            </div>
        </div>
    </div>

    @if($type == 6)
        <div class="card">
            <div class="card-header">
                <h2 class="changeH3">Электрика</h2>
            </div>

            <div class="table-responsive">
                <table class="table text-left">
                    <thead>
                        <tr>
                            <th>Номер в каталоге</th>
                            <th>Артикул</th>
                            <th>Название</th>
                            <th>Колличество</th>
                            <th>Цена за единицу</th>
                            <th>Суммарная цена</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($electric as $item)
                            <tr>
                                <th>{{$item['tm_cid']}}</th>
                                <th>{{$item['tm_sku']}}</th>
                                <th data-bs-toggle="tooltip" data-bs-placement="top" title="{{$item['tm_title']}}">{{mb_strimwidth($item['tm_title'],0,85,"...")}}</th>
                                <th>{{$item['tm_count']}}</th>
                                <th>{{$item['tm_price']}}</th>
                                <th>{{$item['total_price']}} руб.</th>
                            </tr>
                        @endforeach
                    
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="changeH3">Станция</h2>
            </div>

            <div class="table-responsive">
                <table class="table text-left">
                    <thead>
                        <tr>
                            <th>Номер в каталоге</th>
                            <th>Артикул</th>
                            <th>Название</th>
                            <th>Колличество</th>
                            <th>Цена за единицу</th>
                            <th>Суммарная цена</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($station  as $item)
                            <tr>
                                <th>{{$item['tm_cid']}}</th>
                                <th>{{$item['tm_sku']}}</th>
                                <th data-bs-toggle="tooltip" data-bs-placement="top" title="{{$item['tm_title']}}">{{mb_strimwidth($item['tm_title'],0,85,"...")}} руб.</th>
                                <th>{{$item['tm_count']}}</th>
                                <th>{{$item['tm_price']}}</th>
                                <th>{{$item['total_price']}} р</th>
                            </tr>
                        @endforeach
                    
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    @endif
    

@endsection

