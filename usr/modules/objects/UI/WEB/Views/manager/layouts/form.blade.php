{{-- Универсальный wizard --}}
{{--
 Все нумераторы, ID тегов, индексы и тд. Генерируются и добавляются на JS сами.
 Класс css "validate" формы для кастомной валидации
 --}}

<section class="horizontal-wizard">

    <form
            action="{{ ($unit ?? false) ? route('admin.objects.update', $unit) : route('admin.objects.store') }}"
            method="POST"
            enctype="multipart/form-data"

            class="form-wizard {{--validate--}}"
    >
        @csrf
        @if($unit ?? false)
            @method('PUT')
        @endif

        <div class="bs-stepper unit-wizard">

            {{-- header --}}
            <div class="bs-stepper-header">

                {{-- Шаг 1 --}}
                <div class="step" role="tab">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">Основная информация</span>
                            <span class="bs-stepper-subtitle">Полная информация об объекте</span>
                        </span>
                    </button>
                </div>
                {{-- Шаг 1 --}}

                {{-- Шаг 2 --}}
                <div class="step" role="tab">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">Карта</span>
                            <span class="bs-stepper-subtitle">Объект на карте</span>
                        </span>
                    </button>
                </div>
                {{-- Шаг 2 --}}

                {{-- Шаг 3 --}}
                <div class="step" role="tab">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">@lang('support::form.attaches')</span>
                            <span class="bs-stepper-subtitle">@lang('support::form.attaches.description')</span>
                        </span>
                    </button>
                </div>
                {{-- Шаг 3 --}}

                {{-- Шаг 4 --}}
                {{---}}
                <div class="step" role="tab">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title">Разделы</span>
                            <span class="bs-stepper-subtitle">Какие то разделы</span>
                        </span>
                    </button>
                </div>
                {{----}}
                {{-- Шаг 4 --}}

            </div>
            {{-- header --}}

            {{-- content --}}
            <div class="bs-stepper-content">

                {{-- Шаг 1 --}}
                <div class="content step-content" role="tabpanel">
                    <div class="content-header">
                        <h5 class="mb-0">Основная информация</h5>
                        <small class="text-muted">Полная информация об объекте</small>
                    </div>

                    <div class="row">

                        {{-- Название объекта --}}
                        <div class="mb-1 col-md-8">
                            <label class="form-label" for="name">
                                Название объекта
                            </label>
                            <input type="text" id="name" name="name" value="{{ $unit->name ?? '' }}"
                                   class="form-control"
                                   placeholder="Название объекта" required>
                        </div>
                        {{-- Название объекта --}}

                        {{-- Тип объекта --}}
                        <div class="mb-1 col-md-2">
                            <label class="form-label" for="type">Тип объекта</label>
                            <select class="form-select" id="type" name="type">
                                <option value="" @selected(optional($unit ?? false)->type == 1)>Не указан</option>
                                <option value="1" @selected(optional($unit ?? false)->type == 1)>Продажа</option>
                                <option value="2" @selected(optional($unit ?? false)->type == 2)>Проектирование</option>
                                <option value="3" @selected(optional($unit ?? false)->type == 3)>Монтаж</option>
                            </select>
                        </div>
                        {{-- Тип объекта --}}

                        {{-- Статус -}}
                        <div class="mb-1 col-md-2">
                            <label class="form-label" for="status">Статус</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Не указан</option>
                                <option value="1" @selected(optional($unit ?? false)->status == 1)>
                                    Ожидает
                                </option>
                                <option value="2" @selected(optional($unit ?? false)->status == 2)>
                                    Подтвержден
                                </option>
                                <option value="3" @selected(optional($unit ?? false)->status == 3)>
                                    На удалении
                                </option>
                            </select>
                        </div>
                        {{-- Статус --}}

                        {{-- Статус #2 --}}
                        <div class="mb-1 col-md-2">
                            <label class="form-label" for="status_action">Статус</label>
                            <select class="form-select" id="status_action" name="status_action">
                                <option value="">Не указан</option>
                                <option value="1" @selected(optional($unit ?? false)->status == 1)>
                                    Ожидает подтверждения
                                </option>
                                <option value="2" @selected(optional($unit ?? false)->status == 2)>
                                    В работе
                                </option>
                                <option value="3" @selected(optional($unit ?? false)->status == 3)>
                                    Закрыт успешно
                                </option>
                                <option value="4" @selected(optional($unit ?? false)->status == 4)>
                                    Закрыт не успешно
                                </option>
                            </select>
                        </div>
                        {{-- Статус #2 --}}

                        @can('support manager')

                            {{-- Ответственный --}}
                            <div class="mb-1 col-md-12 manager-zone">
                                <label class="form-label" for="uid">Ответственный</label>
                                <select class="form-select select2" id="uid" name="uid">
                                    <option value="">Не указан</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" @selected(optional($unit ?? false)->uid == $u->id)>
                                            {{ user_short_name($u) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Ответственный --}}

                        @endcan

                        {{-- Превью --}}
                        {{---}}
                        <div class="mb-1 col-md-12">
                            <label class="form-label" for="preview">
                                Превью
                            </label>
                            <x-media-library-attachment multiple name="attaches"/>
                        </div>
                        {{----}}
                        {{-- Превью --}}

                    </div>

                    <div class="row d-none">

                        {{-- type --}}
                        {{---}}
                        <div class="mb-1 col-md-2">
                            <label class="form-label" for="obj_type">Type</label>
                            <select class="form-select" id="obj_type" name="obj_type">
                                <option>Тип 1</option>
                                <option>Тип 2</option>
                                <option>Тип 3</option>
                            </select>
                        </div>
                        {{----}}
                        {{-- type --}}

                        {{-- client --}}
                        {{---}}
                        <div class="mb-1 col-md-2">
                            <label class="form-label" for="obj_client">client</label>
                            <select class="form-select" id="obj_client" name="obj_client">
                                <option>obj_client 1</option>
                            </select>
                        </div>
                        {{----}}
                        {{-- client --}}

                        {{-- con --}}
                        {{---}}
                        <div class="mb-1 col-md-2">
                            <label class="form-label" for="obj_con">con</label>
                            <select class="form-select" id="obj_con" name="obj_con">
                                <option>obj_con 1</option>
                            </select>
                        </div>
                        {{----}}
                        {{-- con --}}

                    </div>

                    <div class="row">
                        <div class="mb-1 col-md-12">
                            <label class="form-label" for="comment">Комментарий</label>
                            <textarea class="form-control" id="comment" name="comment"
                                      rows="3" placeholder="Ваш комментарий">{!! $unit->comment ?? '' !!}</textarea>
                        </div>
                    </div>

                </div>
                {{-- Шаг 1 --}}

                {{-- Шаг 2 --}}
                <div class="content step-content" role="tabpanel">
                    <div class="content-header">
                        <h5 class="mb-0">Карта</h5>
                        <small class="text-muted">Координаты и адрес объекта</small>
                    </div>

                    {{-- Карта --}}
                    <div class="row">
                        <div class="mb-1 col-md-12 position-relative">
                            <div id="map" class="h-400 border border-primary br-3"></div>
                            {{-- preloader --}}
                            {{--@include('elements.preloader', ['class' => 'map-preloader d-none'])--}}
                            {{-- preloader --}}
                        </div>
                    </div>
                    {{-- Карта --}}

                    <div id="map-zoom-actions" class="row">
                        <div class="mb-1 col-md-6">
                            <label class="form-label" for="coords">Координаты</label>
                            <input type="text" name="coords" id="coords"
                                   class="form-control"
                                   value="{{ $unit->coords ?? '' }}"
                                   placeholder="Координаты объекта"
                                   readonly
                            >
                        </div>
                        <div class="mb-1 col-md-6">
                            <label class="form-label" for="address">Адрес</label>
                            <div class="input-group">
                                <input type="text" class="form-control geocode"
                                       placeholder="Адрес объекта"
                                       id="address"
                                       name="address"
                                       value="{{ $unit->address ?? '' }}"
                                       aria-describedby="geocode_search"
                                       required
                                >
                                <a class="btn btn-success waves-effect" id="geocode_search">
                                    Найти
                                </a>
                            </div>
                            <div id="obj_notice" class="invalid-feedback"></div>

                        </div>
                    </div>

                </div>
                {{-- Шаг 2 --}}

                {{-- Шаг 3 --}}
                <div class="content step-content" role="tabpanel">
                    <div class="content-header">
                        <h5 class="mb-0">Вложения {ПОКА НЕ РАБОТАЕТ!} </h5>
                        <small class="text-muted">Прикрепление файлов</small>
                    </div>

                    <div class="row">
                        <div class="mb-1 col-md-6">
                            <label for="obj_files" class="form-label">Файлы</label>

                            {{--<input class="form-control" type="file" id="obj_files" name="obj_files[]" multiple />--}}

                            {{--<div name="photo_url" id="photo_url" action="{{ route("admin.objects.store") }}" class="dropzone"></div>--}}
                            {{--<div class="previews"></div>--}}

                            {{--<x-media-library-attachment multiple name="attaches"/>--}}

                        </div>
                        <div class="mb-1 col-md-6">

                            <label class="form-label">Список файлов</label>

                            <ul class="list-group h-max-600">

                                @forelse($files ?? [] as $f)
                                    <li class="list-group-item d-flex align-items-center">
                                        <i data-feather="file" class="font-medium-2 me-1"></i>
                                        <span>Имя файла</span>
                                        {{--<span class="badge bg-primary rounded-pill ms-auto">4</span>--}}
                                    </li>
                                @empty
                                    <li class="list-group-item d-flex align-items-center">
                                        {{--<i data-feather="file" class="font-medium-2 me-1"></i>--}}
                                        <span class="text-center w-100">Файлов нет</span>
                                    </li>
                                @endforelse

                            </ul>
                        </div>
                    </div>

                </div>
                {{-- Шаг 3 --}}

                {{-- Шаг 4 --}}
                {{---}}
                <div class="content step-content" role="tabpanel">
                    <div class="content-header">
                        <h5 class="mb-0">Шаг 4</h5>
                        <small class="text-muted">Шаг 4</small>
                    </div>

                    <div class="row">
                        <div class="mb-1 col-md-6">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control"
                                   placeholder="johndoe">
                        </div>
                        <div class="mb-1 col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                   placeholder="john.doe@email.com" aria-label="john.doe">
                        </div>
                    </div>

                </div>
                {{----}}
                {{-- Шаг 4 --}}

            </div>
            {{-- content --}}

        </div>

    </form>
</section>
{{-- Универсальный wizard --}}

{{-- Необходимые стили --}}
@push('styles')
    <!-- Push Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/wizard/bs-stepper.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-wizard.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endpush
{{-- Необходимые стили --}}

{{-- Необходимые js либы --}}
@push('scripts')
    <!-- Push Page js files -->
    <script src="{{ asset(mix('vendors/js/forms/wizard/bs-stepper.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>

    {{-- Стандартные JS с примерами --}}
    {{--<script src="{{ asset(mix('js/scripts/forms/form-wizard.js')) }}"></script>--}}
    {{--<script src="{{ asset(mix('js/scripts/forms/form-validation.js')) }}"></script>--}}
    {{--<script src="{{ asset(mix('js/scripts/forms/pickers/form-pickers.js')) }}"></script>--}}
    {{-- Стандартные JS с примерами --}}

    <script data-title="Form - unitWizard">

        $(document).ready(function () {
            $('.select2').select2({width: '100%'});
        });

        // Генератор wizard
        let u_wizard = $('.unit-wizard');
        let w_header = u_wizard.children('.bs-stepper-header');
        let w_content = u_wizard.children('.bs-stepper-content');

        let header_step = w_header.children('.step');
        let content_step = w_content.children('.step-content');
        let line = $('<div class="line"><i class="feather-chevron-right font-medium-2"></i></div>')

        // Работа с кнопками действий (Автоматический генератор)
        let button = $('<button type="button" class="btn"></button>'),
            i = $('<i class="align-middle"></i>'),
            span = $('<span class="align-middle d-sm-inline-block d-none"></span>'),

            a_back = button.clone()
                .addClass('btn-outline-secondary btn-prev')
                .prepend(i.clone().addClass('feather-chevron-left me-sm-25 me-0'))
                .append(span.clone().html("{{ trans('objects::lang.wizard.back') }}")),

            a_next = button.clone()
                .addClass('btn-primary btn-next')
                .append(i.clone().addClass('feather-chevron-right ms-sm-25 ms-0'))
                .prepend(span.clone().html("{{ trans('objects::lang.wizard.next') }}")),

            a_send = button.clone()
                .addClass('btn-success btn-submit')
                .attr({'type': 'submit'})
                .prepend(span.clone().html("{{ trans('objects::lang.wizard.save') }}"));

        let left_btn, right_btn,
            container = $('<div></div>').addClass('mt-2 d-flex justify-content-between unit-step-actions');

        header_step.each(function (index) {
            let count = index + 1;

            // Header
            header_step.eq(index)
                .attr({
                    'id': 'unit-step-trigger-' + count,
                    'data-target': '#unit-step-' + count
                })
                .children('button.step-trigger')
                .prepend($('<span class="bs-stepper-box">' + count + '</span>'));

            // Шевроны после шагов во вкладках
            if (count < header_step.length) {
                header_step.eq(index).after(line.clone())
            }

            // Content
            content_step.eq(index)
                .attr({
                    'id': 'unit-step-' + count,
                    'aria-labelledby': 'unit-step-trigger-' + count
                });

            // Actions
            // Первый Tab
            if (index === 0) {
                left_btn = $(a_back).clone().attr('disabled', 'disabled');
                right_btn = $(a_next).clone();
            } else {
                // Все остальные
                left_btn = $(a_back).clone();
                right_btn = $(a_next).clone();
            }

            // Кроме последнего
            if (index === header_step.length - 1) {
                right_btn = $(a_send).clone();
            }

            // Вставляем кнопки действий вместе с блоком
            content_step.eq(index).append($(container).clone().append(right_btn).prepend(left_btn))

        });

        // Работа с wizard'ом
        let bsStepper = document.querySelectorAll('.bs-stepper');
        if (typeof bsStepper !== undefined && bsStepper !== null) {
            for (let el = 0; el < bsStepper.length; ++el) {
                bsStepper[el].addEventListener('show.bs-stepper', function (event) {

                    if (wizardValidator()) {
                        let index = event.detail.indexStep;
                        let numberOfSteps = $(event.target).find('.step').length - 1;
                        let line = $(event.target).find('.step');
                        for (let i = 0; i < index; i++) {
                            line[i].classList.add('crossed');
                            for (let j = index; j < numberOfSteps; j++) {
                                line[j].classList.remove('crossed');
                            }
                        }
                        if (event.detail.to == 0) {
                            for (let k = index; k < numberOfSteps; k++) {
                                line[k].classList.remove('crossed');
                            }
                            line[0].classList.remove('crossed');
                        }
                    } else {
                        // console.log('STOP EVENT');
                        event.preventDefault();
                        return false;
                    }

                });
            }
        }
        let unitWizard = document.querySelector('.unit-wizard');
        if (typeof unitWizard !== undefined && unitWizard !== null) {
            let unitStepper = new Stepper(unitWizard, {
                linear: false
            });
            $(unitWizard)
                .find('.btn-next')
                .on('click', function (e) {
                    if (wizardValidator()) {
                        unitStepper.next();
                    }
                })
                .find('.btn-prev')
                .on('click', function (e) {
                    unitStepper.previous();
                })
                .find('.btn-submit')
                .on('click', function (e) {
                    if (!wizardValidator()) {

                    }
                });
        }

        // ПЕРЕДЕЛАТЬ!!!
        $(document).ready(function() {
            $('form.form-wizard').keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });

        // Кастомный валидатор для Wizard
        function wizardValidator() {

            // Проверяем необходимость валидации
            if ($('form.form-wizard').hasClass('validate')) {
                // Активный step
                let now = $('.step-content.active');

                // Убираем валид при клике
                now.on('click', function (e) {
                    $(e.target).parent().children('.invalid-feedback').remove();
                    $(e.target).removeClass('is-invalid');
                });

                let res = true;
                now.find('input, select, textarea').each(function () {
                    if ($(this).prop('required') && !$(this).val()) {

                        if (!$(this).parent().children('.invalid-feedback')[0]) {
                            $(this).parent().append('<div class="invalid-feedback">Обязательное поле</div>');
                        }

                        $(this).addClass('is-invalid');
                        res = false;
                    } else {
                        // none
                    }
                });
                return res;
            } else {
                return true
            }
        }

    </script>
@endpush
{{-- Необходимые js либы --}}