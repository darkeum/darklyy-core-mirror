<form
        action="@if($unit ?? false){{ route('admin.orgs.update', $unit) }}@else{{ route('admin.orgs.store') }}@endif"
        method="POST" enctype="multipart/form-data"
        class="form"
>
    @csrf

    @if($unit ?? false)
        @method('PUT')
    @endif

    <div class="row">

        {{-- Организация --}}
        <div class="col-7">
            <div class="mb-1">
                <label class="form-label" for="oid">Организация</label>
                <select class="form-select select2" id="oid" name="oid" required>

                   @foreach ($orgs as $org)
                  
							<option value="{{$org->id}}" @selected(isset($unit) && $org->id==$unit->oid)>
                                {{ $org->name }}
                           </option>

                   @endforeach

                </select>

            </div>
        </div>
        {{-- Организация --}}

        {{-- Ответственный --}}
        <div class="col-5">
            <div class="mb-1">
                <label class="form-label" for="uid">Ответственный</label>
                <select class="form-select select2" id="uid" name="uid" required>
                    
                    @foreach ($users_list as $u)

                            <option value="{{$u->id}}" @selected(isset($unit) && $unit->uid == $u->id)>
                                {{ user_short_name($u,true) }}
                            </option>

                    @endforeach

                </select>

            </div>
        </div>
        {{-- Ответственный --}}

        {{-- НДС --}}
        <div class="col-4">
            <div class="mb-1">
                <label class="form-label" for="nds">Процент НДС</label>
                <select class="form-select" id="nds" name="nds" required>
                    
                    @foreach ($nds as $key => $item)

                            <option value="{{$key}}" @selected(isset($unit) && $unit->nds == $key)>
                                {{ $item }}%
                            </option>

                    @endforeach

                </select>

            </div>
        </div>
        {{-- НДС --}}

        {{-- Цена с НДС? --}}
        <div class="col-4">
            <div class="mb-1">
                <label class="form-label" for="nds_includes">Включает ли в себя цена НДС</label>
                <select id="nds_includes" name="nds_includes" class="form-select" required>
                    <option value="1" @selected(isset($unit) && $unit->nds_includes == 1)>Да</option>
                    <option value="0" @selected(isset($unit) && $unit->nds_includes == 0)>Нет</option>

                </select>

            </div>
        </div>
        {{-- Цена с НДС? --}}

        {{-- Префикс --}}
        <div class="col-4">
            <div class="mb-1">
                <label class="form-label" for="form">Префикс</label>
                <input id="prefix" name="prefix" type="text" class="form-control" placeholder="Введите префикс" 
                value="@if($unit ?? false){{ $unit->prefix }}@endif"
                >

            </div>
        </div>
        {{-- Префикс --}}

        @if($unit ?? false)

            

            {{-- Тип налогообложения --}}
                <div class="col-2">
                    <div class="mb-1">
                        <label class="form-label" for="taxation">Тип налогообложения</label>  
                        
                        <select name="taxation" id="taxation" class="form-select">
                            <option value="">Не указано</option>
                            <option value="1" @selected($unit->taxation == 1) >ОСН</option>
                            <option value="2" @selected($unit->taxation == 2) >УСН</option>
                        </select>

                    </div>
                </div>
            {{-- Тип налогообложения --}}
        
            {{-- бухгалтер --}}
                <div class="col-5">
                    <div class="mb-1">
                        <label class="form-label" for="accountant">Бухгалтер</label>  
                        
                        <select class="form-select select2" id="accountant" name="accountant" >
                    
                            @foreach ($users_list as $u)
                            
                                    <option value="{{$u->id}}" @selected($u->id == $unit->accountant)>{{ user_short_name($u,true) }}</option>
        
                            @endforeach
        
                        </select>
        

                    </div>
                </div>
            {{-- бухгалтер --}}

            {{-- Директор --}}
            <div class="col-5">
                <div class="mb-1">
                    <label class="form-label" for="director">Директор</label>  
                    
                    <select class="form-select select2" name="director" id="director" >
                
                        @foreach ($users_list as $u)
                        
                                <option value="{{$u->id}}" @selected($u->id == $unit->director)>{{ user_short_name($u,true) }}</option>
    
                        @endforeach
    
                    </select>
    

                </div>
            </div>
            {{-- Директор --}}

            {{-- Склад --}}
            <div class="col-4">
                <div class="mb-1">
                    <label class="form-label" for="stock_id_default">Склад</label>  
                    
                    <select class="form-select select2" name="stock_id_default" id="stock_id_default" >
                        
                        @foreach ($stocks as $stock)
                        
                                <option value="{{$stock->id}}" @selected($stock->id == $unit->stock_id_default)>{{ $stock->name }}</option>                            
    
                        @endforeach
    
                    </select>
    

                </div>
            </div>
            {{-- Склад --}}

            {{-- Телефон --}}
                <div class="col-4">
                    <div class="mb-1">
                        <label class="form-label" for="phone">Телефон</label>  

                        <input type="tel" id='phone' class="form-control" placeholder="Введите телефон" name="phone" 
                        value="@if($unit ?? false){{$unit->phone}}@endif"
                        >

                    </div>
                </div>
            {{-- Телефон --}}

            {{-- Предупреждение о смене реквизитов --}}
                <div class="col-4">
                    <div class="mb-1">
                        <label class="form-label" for="change_alert">Предупреждение о смене реквизитов</label>  

                        <select name="change_alert" id="change_alert" class="form-select">
                            <option value="0" @selected($unit->change_alert == 0) >Нет</option>
                            <option value="1" @selected($unit->change_alert == 1) >Да</option>
                        </select>

                    </div>
                </div>
            {{-- Предупреждение о смене реквизитов --}}

            {{-- Логотип --}}
            <div class="col-6 text-center">
                <div class="mb-1">
                    <label class="form-label" for="logo">Логотип</label>  

                    <div class="mt-1 mb-2">
                        <img  src="{{ storage_ext_img($unit->logo) }}" height="300" widt="300">
                    </div>                        
                    <input type="file" hidden id="logo" name="logo"
                    
                    >
                    <a id="edit_logo" class="btn btn-secondary waves-effect">Изменить</a>
                </div>
            </div>
            {{-- Логотип --}}

            {{-- Подписи --}}
            <div class="col-6 text-center">
                <div class="mb-1">
                    <label class="form-label" for="bill_sign">Подписи</label>  

                    <div class="mt-1 mb-2">                      
                        <img src="{{ storage_ext_img($unit->bill_sign) }}" height="300" widt="300">
                    </div> 
                    <input type="file" hidden id="bill_sign" name="bill_sign"
                  
                    >
                    <a id="edit_bill_sign" class="btn btn-secondary waves-effect">Изменить</a>
                </div>
            </div>
            {{-- Подписи --}}

        @endif

        {{-- Кнопки --}}
        <div class="card-footer col-12 d-flex justify-content-between">
            <a href="" class="btn btn-outline-secondary waves-effect">Отмена</a>
            <button type="submit" class="btn btn-primary waves-effect waves-float waves-light">
                @if($unit ?? false)
                    Сохранить
                @else
                    Создать
                @endif
            </button>
        </div>
        {{-- Кнопки --}}

    </div>

</form>

{{-- Необходимые стили --}}
@push('styles')
    
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">

@endpush
{{-- Необходимые стили --}}

{{-- Необходимые js либы --}}
@push('scripts')
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/forms/select/i18n/'. app()->getLocale() .'.js') }}"></script>


    <!-- Page js files -->
    <script data-title="Page js files">

        $(".select2").select2({
            placeholder: "Нажмите чтобы выбрать элемент",
        });

        $("#edit_logo").click(function(){
            $("#logo").click();
        });

        $("#edit_bill_sign").click(function(){
            $("#bill_sign").click();
        });

        $('#logo').change(function(){
 
            Swal.fire({
                icon: 'warning',
                title: 'Внимание',
                text: 'Изображение изменится после сохранения!',
            })
            
        });

        $('#bill_sign').change(function(){
    
            Swal.fire({
                icon: 'warning',
                title: 'Внимание',
                text: 'Изображение изменится после сохранения!',
            })
        
        });

        @if(\Session::has('success'))
        {
            Swal.fire({
                icon: 'success',
                title: 'Внимание',
                text: '{!! \Session::get('success') !!}',
                timer: 1500,
            })
        }
        @endif
       
       
    </script>

@endpush
{{-- Необходимые js либы --}}

