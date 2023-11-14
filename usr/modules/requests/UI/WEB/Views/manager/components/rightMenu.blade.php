<div>
    <div class="card">

        <div class="card-header">
            <h5 class="card-title">Постановка задачи</h5>
        </div>

        <div class="card-body profile-suggestion">

            <div class="d-flex justify-content-start align-items-center mb-1">
                <div class="avatar me-1">
                    <img src="{{ avatar($unit->uid_exec) }}" alt="avatar img" height="40"
                         width="40"/>
                </div>
                <div class="profile-user-info">
                    <h6 class="mb-0">{{ ($unit->uid_dir === 0 ? "Гость" : fullUserName($unit->uid_dir)) }}</h6>
                </div>
            </div>

            <div class="d-flex justify-content-center align-items-center mb-1">
                <span class="badge bg-success w-100">
                    <i data-feather="arrow-down" class="me-3"></i>
                    <span>Задача адресована</span>
                    <i data-feather="arrow-down" class="ms-3"></i>
                </span>
            </div>

            <div class="d-flex justify-content-start align-items-center">
                @if(fullUserName($unit->uid_exec) == "Не указан")
                    <div class="profile-user-info p-1 w-100 text-center badge badge-light-dark">
                        <h6 class="mb-0">{{ fullUserName($unit->uid_exec) }}</h6>
                    </div>
                @else
                    <div class="avatar me-1">
                        <img src="{{ avatar($unit->uid_dir) }}" alt="avatar img" height="40"
                             width="40"/>
                    </div>
                    <div class="profile-user-info">
                        <h6 class="mb-0">{{ fullUserName($unit->uid_exec) }}</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="card-title">Объект</h5></div>
        <div class="card-body">
            <livewire:requests::editobjfast :unit="$unit"/>
        </div>    
    </div>
    {{--<livewire:requests::filestree :unit="$unit"/>--}}

    @if($unit->type == 3 || !$unit->uid_exec ||  $unit->status == 2 || $unit->type == 6 || $unit->status == 1)
        <div class="card">

            <div class="card-header">
                <h5 class="card-title">Действия</h5>
            </div>

            <div class="card-body">

                @if(!$unit->uid_exec)
                    <button class="btn btn-success w-100" wire:click="takeReq">
                        Взять задачу
                    </button>
                @endif

                @if($unit->status == 2)
                    <button class="btn btn-danger w-100" wire:click="endReq">
                        Завершить заявку
                    </button>
                @endif

                @if($unit->status == 3 && $unit->type == 3)
                    <button class="btn btn-warning w-100 mt-1" wire:click="editReq">
                        Редактировать
                    </button>
                @endif

                @if($unit->type == 6)
                    <button class="btn btn-info w-100 mt-1" wire:click="print">
                        Распечатать
                    </button>
                @endif

            </div>

            @if( $unit->type == 3 )
                <div class="card-footer">
                    <a class="btn btn-info w-100 " href="{{ url('administration/module/request/view/'.$unit->id) }}">
                        Перейти в старый модуль
                    </a>
                </div>
            @endif

        </div>
    @endif

    @if($unit->type == 3 && ($unit->status == 2 || $unit->status == 3))
        <div class="card">
            <div class="card-body">
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="sku">Номер расчёта</label>
                                <input wire:change="editField($event.target.id, $event.target.value)" type="text"
                                       value="{{ $spec['sku'] ?? false }}" placeholder="Введите номер расчёта"
                                       class="form-control" id="sku" required @if($editForm == false) disabled @endif
                                >
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="title">РПТО НН№</label>
                                <select wire:change="editField($event.target.id, $event.target.value)"
                                        class="form-control" id="title" required
                                        @if($editForm == false) disabled @endif
                                >
                                    {{--<option @if(empty($spec['title'])) selected @endif disabled>Выберите наименование</option>--}}
                                    <option value="">Выберите наименование</option>
                                    @foreach ($array_title as $val)
                                        <option value="{{ $val }}" @if(isset($spec['title']) && $spec['title'] == $val) selected @endif>
                                            {{ $val }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="price">Цена (Без НДС / С НДС)</label>
                                <div class="row">

                                    <div class="col-6">
                                        <input wire:change="editField($event.target.id, $event.target.value)"
                                               type="text" value="{{ $spec['price'] ?? false }}" id="price"
                                               placeholder="Введите цену" class="form-control text-center" required
                                               @if($editForm == false) disabled @endif>
                                    </div>

                                    <div class="col-6">
                                        <select wire:change="editField($event.target.id, $event.target.value)"
                                                class="form-control text-center" id="nds"
                                                @if($editForm == false) disabled @endif
                                        >
                                            <option value="0" @if(isset($spec['nds']) && $spec['nds'] == 0) selected @endif>Без НДС</option>
                                            <option value="1" @if(isset($spec['nds']) && $spec['nds'] == 1) selected @endif>С НДС</option>
                                        </select>
                                    </div>

                                </div>
                                <select wire:change="editField($event.target.id, $event.target.value)"
                                        class="form-control text-center mt-1" id="price_type"
                                        @if($editForm == false) disabled @endif
                                >
                                    @foreach($priceType as $vv)
                                        <option value="{{ $vv->id }}"
                                                @if(isset($spec['price_type']) && $spec['price_type'] == $vv->id) selected @endif>
                                            {{ $vv->title }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="assigment">Назначение</label>
                                <select wire:change="editField($event.target.id, $event.target.value)"
                                        class="form-control" id="assigment" required
                                        @if($editForm == false) disabled @endif
                                >
                                    @foreach($array_assigment as $val)
                                        <option value="{{ $val }}" @if(isset($spec['assigment']) && $spec['assigment'] == $val) selected @endif>
                                            {{ $val }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div class="col-12"
                             style="display: @if(isset($spec['assigment']) && $spec['assigment'] === "ГВС" && $spec['assigment']) @else none @endif ">
                            <div class="mb-1">
                                <label class="form-label" for="assigment2">Назначение 2</label>
                                <select wire:change="editField($event.target.id, $event.target.value)"
                                        class="form-control" id="assigment2"
                                        @if(isset($spec['assigment']) && $spec['assigment'] === "ГВС") @else disabled @endif required
                                        @if($editForm == false) disabled @endif>

                                    @foreach($array_assigment2 as $val){

                                    <option value="{{ $val }}"
                                            @if(isset($spec['assigment2']) && $spec['assigment2'] == $val ) selected @endif>{{ $val }}</option>
                                    }
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="diameter">Диаметр (мм)</label>
                                <input wire:change="editField($event.target.id, $event.target.value)" type="number"
                                       class="form-control" id="diameter" value="{{$spec['diameter'] ?? false}}"
                                       placeholder="Введите диаметр" required @if($editForm == false) disabled @endif>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="burden">Q нагрузка</label>
                                <input wire:change="editField($event.target.id, $event.target.value)" type="text"
                                       step="any" class="form-control" id="burden" value="{{$spec['burden'] ?? false}}"
                                       placeholder="Введите нагрузку" required @if($editForm == false) disabled @endif>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="units">Q единицы измерения</label>
                                <select wire:change="editField($event.target.id, $event.target.value)"
                                        class="form-control" id="units" required
                                        @if($editForm == false) disabled @endif
                                >
                                    {{--<option value="" @if(empty($spec['units'])) selected @endif disabled>Выберите единицы измерения</option>--}}
                                    <option value="">Выберите единицы измерения</option>
                                    @foreach($array_units as $val)
                                        <option value="{{ $val }}" @if(isset($spec['units']) && $spec['units'] == $val) selected @endif>
                                            {{ $val }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="square">Площадь (м<sup>2</sup>)</label>
                                <input wire:change="editField($event.target.id, $event.target.value)" type="text"
                                       id="square" class="form-control" value="{{ $spec['square'] ?? false }}"
                                       placeholder="Введите площадь" required @if($editForm == false) disabled @endif>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="plates">Пластин (шт)</label>
                                <input wire:change="editField($event.target.id, $event.target.value)" type="number"
                                       id="plates" class="form-control" value="{{ $spec['plates'] ?? false}}"
                                       placeholder="Введите пластин" required @if($editForm == false) disabled @endif>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="pressure">Давление (МПа)</label>
                                <input wire:change="editField($event.target.id, $event.target.value)" type="text"
                                       id="pressure" class="form-control" value="{{ $spec['pressure'] ?? false}}"
                                       placeholder="Введите давление" required @if($editForm == false) disabled @endif>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="temperature">Температура (&deg;C)</label>
                                <input wire:change="editField($event.target.id, $event.target.value)" type="number"
                                       id="temperature" class="form-control" value="{{ $spec['temperature'] ?? false }}"
                                       placeholder="Введите температуру" required
                                       @if($editForm == false) disabled @endif>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="weight">Масса (кг)</label>
                                <input wire:change="editField($event.target.id, $event.target.value)" type="text"
                                       id="weight" class="form-control" value="{{ $spec['weight'] ?? false }}"
                                       placeholder="Введите массу" required @if($editForm == false) disabled @endif>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>

        window.addEventListener('swal', function (e) {
            Swal.fire(e.detail);
        });

        window.addEventListener('printTable', function (e) {

            let body = $("body");

            body.addClass("printSelected").append('<div class="printSelection"></div>');

            $("#printTable").clone().appendTo(".printSelection");

            window.print();

            body.removeClass("printSelected");
            $(".printSelection").remove();


        });

        window.addEventListener('editField', function (e) {
            Swal.fire({
                position: 'bottom-end',
                icon: 'success',
                title: 'Поле изменено',
                showConfirmButton: false,
                timer: 1000,
                toast: true
            })
        });

        window.addEventListener('edit', function (e) {
            Swal.fire({
                title: "Внимание!",
                html: "В продажах, в которых присутствует данный товар, не будет изменен",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Отмена',
                confirmButtonText: 'Продолжить',
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit("editReq", 1);
                }
            });
        });

        window.addEventListener('take', function (e) {
            Swal.fire({
                title: "Взять заявку?",
                html: false,
                icon: 'question',
                showCancelButton: true,
                cancelButtonText: 'Нет',
                confirmButtonText: 'Да',
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit("takeReq", 1);
                }
            });
        });

    </script>
@endpush
