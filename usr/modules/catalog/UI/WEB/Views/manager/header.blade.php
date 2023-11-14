<div class="row">

    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"> Каталог товаров</h4>
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.catalog.index') ? 'active' : '' }}"
                           href="{{ route('admin.catalog.index') }}">Список товаров</a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.catalog.pump.control') ? 'active' : '' }}"
                           href="{{ route('admin.catalog.pump.control') }}">Насосы</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.catalog.pump.equate') ? 'active' : '' }}"
                           href="{{ route('admin.catalog.pump.equate') }}">Приравнивание</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

   

</div>
