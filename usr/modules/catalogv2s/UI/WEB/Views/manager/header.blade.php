<div class="row">

    <div class="col-12 col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Каталог товаров</h4>
                <ul class="nav nav-pills">

                    {{-- <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.catalogv2s.index') ? 'active' : '' }}"
                            href="{{ route('admin.catalogv2s.index') }}">
                            Главная
                        </a>
                    </li> --}}

                    <li class="nav-item">
                        <a class="nav-link {{ isset($pumpCheck) ? 'active' : '' }}"
                           href="{{ route('admin.catalogv2s.pump') }}">
                            Насосы
                        </a>
                    </li>                    

                    @if(isset($pumpCheck))
                    
                            <li class="nav-item ms-auto">
                                <button type="button" class="nav-link active" onclick="openModal()" data-toggle="modal" data-target="#exampleModal">
                                    Добавить
                                </button>
                            </li>
                        
                    @endif

                </ul>
            </div>
        </div>
    </div>
    

</div>


