{{-- Модификаторы таблицы --}}
<button id="aGrid-modifier"
        type="button"
        class="btn btn-sm btn-icon btn-flat-secondary waves-effect"
        title="Модификаторы таблицы"
        {{--data-bs-original-title="Модификаторы таблицы"--}}
        {{--data-bs-toggle="tooltip"--}}
        {{--data-bs-placement="left"--}}
>
    <i class="feather-cpu"></i>
</button>

{{-- Параметры таблицы --}}
<div id="aGrid-modifiers" class="d-none">

    <ul class="list-group search-options-popover-content">

        {{-- Блокировка прокрутки таблицы --}}
        <li class="list-group-item d-flex align-items-center">
            <span>Блокировка</span>
            <span class="ms-auto">
                <div class="form-check form-check-primary form-switch">
                    <input class="form-check-input search-options-anchor"
                           name="aGrid-blocked" type="checkbox"
                           id="aGrid-blocked"
                           data-aGrid-modifier="blocked"
                           title="Блокировка прокрутки страницы когда курсор над таблицей"
                    />
                </div>
            </span>
        </li>
        {{-- Блокировка прокрутки таблицы --}}

        {{-- Быстрый просмотр --}}
        <li class="list-group-item d-flex align-items-center">
            <span>Быстрый просмотр</span>
            <span class="ms-auto">
                <div class="form-check form-check-primary form-switch">
                    <input class="form-check-input search-options-anchor"
                           name="aGrid-visible" type="checkbox"
                           id="aGrid-visible"
                           data-aGrid-modifier="visible"
                           title="Быстрый просмотр строки"
                    />
                </div>
            </span>
        </li>
        {{-- Быстрый просмотр --}}

    </ul>

</div>
{{-- Параметры таблицы --}}

{{-- Модификаторы таблицы --}}