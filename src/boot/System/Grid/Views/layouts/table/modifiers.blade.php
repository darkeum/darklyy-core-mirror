{{-- Модификаторы таблицы --}}

<button id="g-modifier"
        type="button"
        class="btn btn-icon btn-flat-secondary waves-effect"
        {{--class="btn btn-icon btn-outline-primary waves-effect"--}}
        title="Модификаторы таблицы"
        data-bs-original-title="Модификаторы таблицы"
        data-bs-toggle="tooltip"
>
    {{--<i class="feather-settings"></i>--}}
    <i class="feather-cpu"></i>
</button>

{{-- Параметры таблицы --}}
<div id="g-modifiers" class="d-none">

    <ul class="list-group search-options-popover-content">

        {{-- Блокировка прокрутки таблицы --}}
        <li class="list-group-item d-flex align-items-center">
            <span>Блокировка</span>
            <span class="ms-auto">
                <div class="form-check form-check-primary form-switch">
                    <input class="form-check-input search-options-anchor"
                           name="table-blocked" type="checkbox"
                           id="table-blocked"
                           data-table="blocked"
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
                           name="table-visible" type="checkbox"
                           id="table-visible"
                           data-table="visible"
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