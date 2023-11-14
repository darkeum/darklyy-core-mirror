{{-- sidebar-content --}}
<div class="sidebar-content">

    <span class="sidebar-close-icon">
        <i data-feather="x"></i>
    </span>

    {{-- Sidebar header start --}}
    <div class="chat-fixed-search">
        <div class="d-flex align-items-center w-100">
            {{--<h4 class="chat-list-title w-100 text-center">--}}
            {{--Информация--}}
            {{--</h4>--}}
            {{--<button type="button" class="btn btn-icon btn-warning waves-effect waves-float waves-light ms-auto d-none btn-actions">--}}
            {{--<i data-feather="menu"></i>--}}
            {{--</button>--}}
        </div>
    </div>
    {{-- Sidebar header end --}}

    {{-- Sidebar Users start --}}
    <div class="chat-user-list-wrapper">

        {{-- Новый список - Информации --}}
        <div class="card shadow-none m-0">
            <div class="card-body">

                {{-- preloader --}}
                @include('elements.preloader', ['wire' => true])
                {{-- preloader --}}

                {{-- Редактирование --}}
                @if ($editable)

                    <h5 class="mb-75 text-primary text-center">
                        Редактирование
                    </h5>

                    {{-- Заголовок заявки --}}
                    <div class="col-12">
                        <label class="form-label" for="title">Заголовок заявки</label>
                        <input type="text" wire:model="support.title" class="form-control" id="title"/>
                    </div>
                    {{-- Заголовок заявки --}}

                    {{-- Тип заявки --}}
                    <div class="col-12 mt-1">
                        <label class="form-label" for="type">Тип заявки</label>
                        <select wire:model="support.type" class="form-select" id="type">
                            @foreach ($types as $type_key => $type)
                                <option value="{{ $type_key }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Тип заявки --}}

                    {{-- Место обнаружения --}}
                    <div class="col-12 mt-1">
                        <label class="form-label" for="place">Место обнаружения</label>
                        <input type="text" wire:model="support.place" class="form-control" id="place"/>
                    </div>
                    {{-- Место обнаружения --}}

                    {{-- URL --}}
                    <div class="col-12 mt-1">
                        <label class="form-label" for="url">URL</label>
                        <div class="input-group">
                            <input type="text" wire:model="support.url" class="form-control" id="url"
                                   title="{{ $support->url }}"/>
                            <a href="{{ $support->url }}" target="_blank" class="btn btn-outline-secondary">
                                <i class="feather-link-2"></i>
                            </a>

                        </div>
                    </div>
                    {{-- URL --}}

                    {{-- Приоритет --}}
                    <div class="col-12 mt-1">
                        <label class="form-label" for="mark">Приоритет</label>
                        <select wire:model="support.mark" class="form-select" id="mark">
                            @foreach ($marks as $mark_key => $mark)
                                <option value="{{ $mark_key }}">{{ $mark }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Приоритет --}}

                    {{-- Дата обнаружения --}}
                    <div class="col-12 mt-1">
                        <label class="form-label" for="view">Дата обнаружения</label>
                        <input type="datetime-local" wire:model.lazy="view" class="form-control" id="view"/>
                    </div>
                    {{-- Дата обнаружения --}}

                    {{-- Планируемая дата --}}
                    @can('support manager')
                        <div class="col-12 mt-1">
                            <label class="form-label" for="planned">Планируемая дата</label>
                            <input type="datetime-local" wire:model.lazy="planned" class="form-control" id="planned"/>
                        </div>
                    @endcan
                    {{-- Планируемая дата --}}

                    {{-- Дейсвтия --}}
                    <div class="row mt-1">

                        <div class="col-md-3">

                            <button type="button" wire:click="back"
                                    class="btn btn-icon btn-secondary waves-effect waves-float waves-light w-100"
                                    title="Вернуться"
                            >
                                <i class="feather-arrow-left"></i>
                            </button>
                        </div>
                        <div class="col-md-9">
                            <button type="button" wire:click="save"
                                    class="btn btn-success waves-effect waves-float waves-light w-100">
                                <i class="feather-save"></i>
                                Сохранить
                            </button>
                        </div>
                    </div>
                    {{-- Дейсвтия --}}

                @else

                    <h5 class="mb-75 text-primary text-center">
                        Информация
                    </h5>
                    <p class="card-text d-none">
                        Описание заявки или доп. информация
                    </p>

                    {{-- Тип заявки --}}
                    <div class="col-12 mt-0">
                        <h5 class="">Тип заявки:</h5>
                        <span class="card-text">
                            {{ isset($support->type) ? app(\Support\Tasks\TypesTask::class)->run($support->type, 'default') : 'Не указано' }}
                        </span>
                    </div>
                    {{-- Тип заявки --}}

                    {{-- Место обнаружения --}}
                    <div class="col-12 mt-1">
                        <h5 class="mb-0">Место обнаружения:</h5>
                        <span class="card-text">
                            {{ $support->place ?? 'Не указано' }}
                        </span>
                    </div>
                    {{-- Место обнаружения --}}

                    {{-- URL --}}
                    <div class="col-12 mt-1">
                        <h5 class="mb-0">URL:</h5>
                        <span class="card-text">
                            <a href="{{ $support->url ?? '' }}" target="_blank">
                                {{ $support->url ?? 'Не указано' }}
                            </a>
                        </span>
                    </div>
                    {{-- URL --}}

                    {{-- Приоритет --}}
                    <div class="col-12 mt-1">
                        <h5 class="mb-0">Приоритет:</h5>
                        <span class="card-text">
                            {!! isset($support->mark) ? app(\Support\Tasks\MarksTask::class)->run($support->mark, 'badge') : 'Не указано' !!}
                        </span>
                    </div>
                    {{-- Приоритет --}}

                    {{-- Этапы обработки --}}
                    <div class="col-12 mt-1">
                        <h5 class="mb-0">Этапы обработки:</h5>
                        <ul class="timeline-mini pt-50 mb-2">

                            {{-- Обнаружение --}}
                            @if ($support->view_at)
                                <li class="timeline-item">

                                    @if ($support->planned_at or $support->completed_at)
                                        {{--<div class="timeline-badge bg-success"></div>--}}
                                        <i class="timeline-badge feather-check text-success"></i>
                                    @else
                                        <div class="timeline-badge bg-danger blob red"></div>
                                    @endif

                                    <div>
                                        <strong>
                                            <span class="{{ ($support->planned_at or $support->completed_at) ? 'text-success' : 'text-primary' }}">
                                                {{ \Boot\Support\Carbon::parse($support->view_at)->format("d.m.Y h:m:s") ?: 'Не указано' }}
                                            </span>

                                        </strong>
                                        <small class="d-block text-muted">
                                            Обнаружение
                                        </small>
                                    </div>
                                </li>
                            @endif
                            {{-- Обнаружение --}}

                            {{-- Планирование --}}
                            @if ($support->planned_at and !$support->started_at)
                                <li class="timeline-item">

                                    @if ($support->planned_at and !$support->started_at)
                                        <div class="timeline-badge bg-warning blob orange"></div>
                                    @else
                                        <i class="timeline-badge feather-check text-success"></i>
                                    @endif

                                    <div>
                                        <strong>
                                            @if ($support->planned_at and !$support->started_at)
                                                <span class="text-warning">
                                                    {{ \Boot\Support\Carbon::parse($support->planned_at)->format("d.m.Y h:m:s") ?: 'Не указано' }}
                                                </span>
                                            @else
                                                <span class="text-success">
                                                    Работа начата сразу
                                                </span>
                                            @endif
                                        </strong>
                                        <small class="d-block text-muted">
                                            Планирование
                                        </small>
                                    </div>
                                </li>
                            @endif
                            {{-- Планирование --}}

                            {{-- Начало работ --}}
                            @if ($support->started_at or $support->completed_at)
                                <li class="timeline-item">

                                    @if ($support->started_at and !$support->completed_at)
                                        <div class="timeline-badge bg-info blob blue"></div>
                                    @else
                                        <i class="timeline-badge feather-check text-success"></i>
                                    @endif

                                    <div>
                                        <strong>
                                            @if ($support->started_at and !$support->completed_at)
                                                <span class="text-info">
                                                    {{ \Boot\Support\Carbon::parse($support->started_at)->format("d.m.Y h:m:s") ?: 'Не указано' }}
                                                </span>
                                            @else
                                                <span class="text-success">
                                                    Решение принято сразу
                                                </span>
                                            @endif
                                        </strong>
                                        <small class="d-block text-muted">
                                            В работе
                                        </small>
                                    </div>
                                </li>
                            @endif
                            {{-- Начало работ --}}

                            {{-- Завершение работ --}}
                            @if ($support->completed_at)
                                <li class="timeline-item">
                                    {{--<div class="timeline-badge bg-success"></div>--}}
                                    <i class="timeline-badge feather-check text-success"></i>
                                    <div>
                                        <strong>
                                        <span class="text-success">
                                            {{ \Boot\Support\Carbon::parse($support->completed_at)->format("d.m.Y h:m:s") ?: 'Не указано' }}
                                        </span>
                                        </strong>
                                        <small class="d-block text-muted">
                                            Завершение
                                        </small>
                                    </div>
                                </li>
                            @endif
                            {{-- Завершение работ --}}

                            {{---}}
                            @if ($support->status)
                                <li class="timeline-item flex-column">
                                    <div class="timeline-badge"></div>
                                    <div>
                                        <strong>
                                            <span class="text-primary">
                                                Статус
                                            </span>
                                        </strong>
                                        {!! isset($support->status) ? app(\Support\Tasks\StatusTask::class)->run($support->status, 'badge') : 'Не указано' !!}
                                    </div>
                                </li>
                            @endif
                            {{----}}

                        </ul>

                        {!! isset($support->status) ? app(\Support\Tasks\StatusTask::class)->run($support->status, 'badge') : 'Не указано' !!}

                    </div>
                    {{-- Этапы обработки --}}

                    {{-- Кнопка - Редактирование --}}
                    @if ($support->status != 3)
                        <div class="hr-text my-1">
                            <span>Редактирование</span>
                        </div>

                        {{-- preloader --}}
                        {{--@include('elements.preloader', ['wire' => true])--}}
                        {{-- preloader --}}

                        <button type="button" wire:click="edit" class="btn btn-warning w-100 align-baseline">
                            <i class="feather-edit"></i>
                            Изменить
                        </button>
                    @endif
                    {{-- Кнопка - Редактирование --}}

                @endif
                {{-- Редактирование --}}

                {{-- Кнопки - Действий --}}
                @can('support manager')

                    @switch($status)
                        @case(1)
                            <div class="hr-text my-1">
                            <span>Действия</span>
                        </div>
                            <div class="row">
                            <div class="col-md-6">
                                <button type="button" wire:click="start"
                                        class="btn btn-info w-100 align-baseline">
                                    <i class="feather-thumbs-up"></i>
                                    Взять
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" wire:click="cancel"
                                        class="btn btn-danger w-100 align-baseline">
                                    <i class="feather-thumbs-down"></i>
                                    Отклонить
                                </button>
                            </div>
                        </div>
                        @break
                        @case(2)
                            <div class="hr-text my-1">
                            <span>Завершение работы</span>
                        </div>
                            <button type="button" wire:click="complete" class="btn btn-success w-100 align-baseline">
                            <i class="feather-check"></i>
                            Завершить
                        </button>
                        @break
                        @case(3)
                            <div class="hr-text my-1">
                            <span>Возобновить работу</span>
                        </div>
                            <button type="button" wire:click="resume" class="btn btn-info w-100 align-baseline">
                            <i class="feather-refresh-cw"></i>
                            Возобновить
                        </button>
                        @break
                    @endswitch

                @endcan
                {{-- Кнопки - Действий --}}

            </div>
        </div>
        {{--/ Новый список - Информации --}}

    </div>
    {{-- Sidebar Users end --}}

</div>
{{-- sidebar-content --}}

{{--@push('scripts')--}}
{{--@endpush--}}