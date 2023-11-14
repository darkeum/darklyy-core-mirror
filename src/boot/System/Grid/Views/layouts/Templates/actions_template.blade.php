{{-- Заготовка под actions --}}
<div class="btn-group dropdown dropstart" role="group">

    {{-- Кнопка Шеврон --}}
    <a class="btn btn-sm btn-outline-secondary dropdown-toggle-split"
       data-bs-toggle="dropdown" aria-expanded="false"
    >
        <i class="feather-chevron-left"></i>
    </a>
    {{-- Кнопка Шеврон --}}

    {{-- Дроп меню --}}
    <div class="dropdown-menu p-0">
        {{--<h6 class="dropdown-header text-center">Действия</h6>--}}

        {{-- Тестовое меню --}}
        <a class="dropdown-item">
            <i class="feather-file-text me-50"></i> Уровень 1
        </a>
        <div class="dropdown-menu dropdown-submenu">
            <a class="dropdown-item" href="#">Multi level 1</a>
            <a class="dropdown-item" href="#">Уровень 2</a>
            <div class="dropdown-menu dropdown-submenu">

                <a class="dropdown-item" href="#">Multi level 1</a>
                <a class="dropdown-item" href="#">Multi level 2</a>

                <a class="dropdown-item" href="#">Уровень 3</a>
                <div class="dropdown-menu dropdown-submenu">

                    <a class="dropdown-item" href="#">Multi level 1</a>
                    <a class="dropdown-item" href="#">Multi level 2</a>

                </div>

            </div>

        </div>
        {{-- Тестовое меню --}}

    </div>
    {{-- Дроп меню --}}

    {{-- Кнопка --}}
    <a class="btn btn-sm btn-outline-secondary">
        <i class="feather-eye"></i>
    </a>
    {{-- Кнопка --}}

</div>
{{-- Заготовка под actions --}}