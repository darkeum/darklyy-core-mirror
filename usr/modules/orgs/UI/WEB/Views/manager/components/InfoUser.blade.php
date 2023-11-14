<div class="col-3">
    <div class="card">
        <div class="card-body">
            <div class="user-avatar-section mb-2">
                <div class="d-flex align-items-center flex-column">
                    <img class="img-fluid rounded mt-3 mb-2" src="{{ avatar($unit->uid) }}" height="110" width="110">
                    <h4>@if($user ?? false) {{ user_short_name($user,true) }} @else Не выбран @endif</h4>
                    <span class="badge bg-light-secondary">
                        @switch($field)
                            @case("director")
                                Директор
                                @break
                            @case("accountant")
                                Бухгалтер
                                @break
                            @case("uid")
                                Ответственный
                                @break
                        @endswitch
                    </span>
                </div>
            </div>
            <h4 class="fw-bolder border-bottom pb-50 mb-1">Информация о пользователе</h4>
            <div class="info-container">
                <ul class="list-unstyled">
                    <li class="mb-75">
                        <span class="fw-bolder me-25">Email:</span>
                        @if($user ?? false )
                            <span class="badge bg-light-success">{{ $user->email }}</span>
                        @else
                            <span class="badge bg-light-danger">Нет информации</span>
                        @endif
                    </li>
                    <li class="mb-75">
                        <span class="fw-bolder me-25">Статус:</span>
                        @if($user ?? false and $user->active == 1)
                            <span class="badge bg-light-success">Активный</span>
                        @else
                            <span class="badge bg-light-danger">Не активный</span>
                        @endif
                    </li>
                    <li class="mb-75">
                        <span class="fw-bolder me-25">Телефон:</span>
                        @if($user->phone ?? false)
                            <span>{{ $user->phone }}</span>
                        @else
                            <span class="badge bg-light-danger">Нет информации</span>
                        @endif
                    </li>
                    <li class="mb-75">
                        <span class="fw-bolder me-25">Дата последнего визита:</span>
                        @if($user ?? false )
                            <span>{{ \Illuminate\Support\Carbon::parse($user->last_visit)->format('d.m.Y')}}</span>
                        @else
                            <span class="badge bg-light-danger">Нет информации</span>
                        @endif                       
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>



