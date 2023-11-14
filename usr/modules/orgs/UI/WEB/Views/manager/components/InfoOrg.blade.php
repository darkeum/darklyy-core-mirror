<div class="col-3">

    <div class="card">
        <div class="card-body">
            <div class="user-avatar-section mb-2">
                <div class="d-flex align-items-center flex-column">
                    <img class="img-fluid rounded mt-3 mb-2" src="{{ storage_ext_img($logo) }}" height="110" width="110">
                    <h4>{{ $unit->name }}</h4>
                    <span class="badge bg-light-secondary">
                        Организация
                    </span>
                </div>
            </div>
            <h4 class="fw-bolder border-bottom pb-50 mb-1">Информация о организации</h4>
            <div class="info-container">
                <ul class="list-unstyled">                    
                    <li class="mb-75">
                        <span class="fw-bolder me-25">ИНН:</span>
                        <span> {{ $unit->inn }} </span>
                    </li>
                    <li class="mb-75">
                        <span class="fw-bolder me-25">КПП:</span>
                        @if($unit->kpp ?? false)
                            <span>{{ $unit->kpp }}</span>
                        @else
                            <span class="badge bg-light-danger">Нет информации</span>
                        @endif
                    </li>
                    <li class="mb-75">
                        <span class="fw-bolder me-25">ОГРН:</span>
                        @if($unit->ogrn ?? false)
                            <span>{{ $unit->ogrn }}</span>
                        @else
                            <span class="badge bg-light-danger">Нет информации</span>
                        @endif
                    </li>
                    <li class="mb-75">
                        <span class="fw-bolder me-25">Телефон:</span>
                        @if($unit->phone ?? false)
                            <span>{{ $unit->phone }}</span>
                        @else
                            <span class="badge bg-light-danger">Нет информации</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>



