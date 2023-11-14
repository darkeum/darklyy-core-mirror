<div class="row">
    <div class="col-4 ">
        <div class="card">


        <div class="card-header">
            <h2>Ввод данных</h2>
        </div>
        <div class="card-body">
            <form class="form form-vertical" wire:submit.prevent="search">

                <div class="row ">
                    <div class="mb-1 col-6">
                        <label class="form-label" for="first_field">Исходный насос</label>
                        <textarea class="form-control" id="first_field" wire:model="first_field" rows=1000000 placeholder="Введите ID" style="height: 158px;"></textarea>
                    </div>
                    <div class="mb-1 col-6">
                        <label class="form-label" for="second_field">Насос назначения</label>
                        <textarea class="form-control" id="second_field" wire:model="second_field" rows="3" placeholder="Введите ID" style="height: 158px;"></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-1 waves-effect waves-float waves-light">Найти</button>
                    </div>
                </div>
            </form>
        </div>


        </div>
    </div>
    <div class="col-8">
        <div class="card">
            <div class="card-header">
                <h2>Вывод данных</h2>
            </div>

            <div class="card-body">
                @if($check == false)
                <h5 class="text-info">Введите данные</h5>
                @else
                <div class="row">
                    <div class="col-12">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Артикул</th>
                                <th scope="col">Название</th>
                                <th scope="col"></th>
                                <th scope="col">Артикул</th>
                                <th scope="col">Название</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                                @for($id=0; $id<$max_count; $id++)
                                    @if(isset($array_first_field[$id]) || isset($array_second_field[$id]))
                                    <tr rowspan="2">
                                        @if(isset($array_first_field[$id]))<td  data-bs-toggle="tooltip" data-bs-placement="top" title="{{$array_first_field[$id]['sku']}}">{{$array_first_field[$id]['id']}}<br><br>{{$array_first_field[$id]['sku']}}</td> @else <td></td> @endif
                                        @if(isset($array_first_field[$id]))<td  data-bs-toggle="tooltip" data-bs-placement="top" title="{{$array_first_field[$id]['title']}}">{{$array_first_field[$id]['title']}}</td> @else <td></td> @endif
                                        <td></td>
                                        @if(isset($array_second_field[$id])) <td data-bs-toggle="tooltip" data-bs-placement="top" title="{{$array_second_field[$id]['sku']}}">{{$array_second_field[$id]['id']}}<br><br>{{$array_second_field[$id]['sku']}}</td> @else <td></td> @endif
                                        @if(isset($array_second_field[$id])) <td data-bs-toggle="tooltip" data-bs-placement="top" title="{{$array_second_field[$id]['title']}}">{{$array_second_field[$id]['title']}}</td> @else <td></td> @endif
                                        @if(isset($array_second_field[$id])) <td data-bs-toggle="tooltip" data-bs-placement="top" @if($array_second_field[$id]['stat_insert']==1) title="Поставьте галочку чтобы добавить" @else title="Поставьте галочку чтобы перезаписать" @endif><input type="checkbox" @if($array_second_field[$id]['insert']==1) checked @endif class="form-check-input" id="colorCheck5" wire:click="skip_add({{$array_second_field[$id]['key']}})")></td> @else <td></td> @endif
                                    </tr>
                                    @endif
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    {{-- <div class="col-6">
                        <table class="table">
                            <thead>
                            <tr rowspan="2">
                                <th scope="col">Артикул</th>
                                <th scope="col">Название</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($array_second_field as $item)
                                <tr>
                                    <td data-bs-toggle="tooltip" data-bs-placement="top" title="{{$item['sku']}}">{{$item['sku']}}</td>
                                    <td data-bs-toggle="tooltip" data-bs-placement="top" title="{{$item['title']}}">{{mb_strimwidth($item['title'],0,28,"...")}}</td>
                                    <td data-bs-toggle="tooltip" data-bs-placement="top" @if($item['stat_insert']==1) title="Поставьте галочку чтобы добавить" @else title="Поставьте галочку чтобы перезаписать" @endif><input type="checkbox" @if($item['insert']==1) checked @endif class="form-check-input" id="colorCheck5" wire:click="skip_add({{$item['key']}})")></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> --}}

                  
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" wire:click.prevent="submit" class="btn btn-primary me-1 waves-effect waves-float waves-light">Прировнять</button>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="modal fade @if($gratz === true) show @endif"
         id="myExampleModal"
         style="display: @if($gratz === true)
                 block
         @else
                 none
         @endif;"
         tabindex="-1"
         role="dialog"
         aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role" role="document">
          <div class="modal-content">
            <div class="modal-header bg-transparent">
              <button type="button" class="btn-close" wire:click="close()"></button>
            </div>
            <div class="modal-body">
                <div class="col-12">
                    <h2> @if($validate_message!=""){{$validate_message}} @else Данные добавлены успешно @endif</h2>
                </div>
                <div class="col-12 text-center mt-2">                 
                  <button type="button" class="btn btn-outline-secondary" wire:click="close()">
                    Ок
                  </button>
                </div>
            </div>
          </div>
        </div>
    </div>

</div>