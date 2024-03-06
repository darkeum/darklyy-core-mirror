{{-- # ИСПОЛЬЗОВАНИЕ АТОМАТИЧЕСКОГО ACTIONS --}}
{{--

$row - Обязательная переменная строки Grid !!!

https://app.weeek.net/ws/288286/document/12

--}}

{{-- Автоматический actions генератор --}}
@php

    // Конфиг
    $actions_cfg = config('grid.actions'); // Переделать на мультиязычность

    // Фильтруем от false, если ЕСТЬ то убираем
    $actions = array_filter($actions, function ($elem) {
        return $elem == true;
    });

    // Проверка на тип данных
    $marker = function ($key) use ($actions_cfg) {
        $marker = $key;
        if (is_int($key)) {
            $marker = 'html';
        } else {
            if (!array_key_exists($key, $actions_cfg)) {
                $marker = 'custom';
            }
        }
        return $marker;
    };

    $destroy = array_search('destroy', array_keys($actions));
    $actions_keys = array_keys($actions);
    $count = count($actions);

    $id = $row['id'];

@endphp

@if($destroy ?? false)
    <form action="{{ route('admin.'. $module .'.destroy', $id) }}" method="POST"
          onsubmit="return confirm('Удалить ?');"
    >
        @csrf
        @method('DELETE')
        @endif

        <div class="btn-group {{ ($count >= 2) ? 'dropstart' : '' }}" role="group">

            {{-- Дроп список - Если больше чем 2 --}}
            @if($count > 2)

                <a class="btn btn-sm btn-outline-secondary dropdown-toggle-split"
                   data-bs-toggle="dropdown"
                   aria-expanded="false"
                >
                    <i class="feather-chevron-left"></i>
                </a>

                <div class="dropdown-menu p-0">

                    {{--<h6 class="dropdown-header text-center">Действия</h6>--}}
                    @foreach(array_slice($actions, 1) as $key => $action)

                        @switch($marker($key))

                            @case('destroy')
                                <form action="{{ route('admin.'. $module .'.destroy', $id) }}" method="POST"
                                      onsubmit="if(confirm('Удалить ?')){ return true }else{ return false }"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button class="dropdown-item w-100" type="submit">
                                        <i data-feather="trash" class="me-50"></i>
                                        Удалить
                                    </button>
                                </form>
                                @break

                            @case('custom')
                                <a href="{{ $actions[$key]['url'] }}"
                                   target="_blank"
                                   class="dropdown-item"
                                >
                                    <i class="feather-{{ $actions[$key]['icon'] }} me-50"></i>
                                    {{ $actions[$key]['text'] }}
                                </a>

                                @break

                            @case('html')
                                {!! view('grid::layouts.actions.html', [
                                        'type' => 'drop',
                                        'html' => $action
                                ])->render() !!}
                                @break

                            @case('print')
                                <a href="{{ $actions[$key] }}"
                                   target="_blank"
                                   class="dropdown-item"
                                >
                                    <i class="feather-{{ $actions_cfg[$key][1] }} me-50"></i> {{ $actions_cfg[$key][0] }}
                                </a>
                                @break

                            @case('export')
                                @break

                                {{-- show, edit, destroy --}}
                            @default
                                <a href="{{ (is_bool($actions[$key])) ? route('admin.'. $module .'.'. $key, $id) : $actions[$key] }}"
                                   target="_blank"
                                   class="dropdown-item"
                                >
                                    <i class="feather-{{ $actions_cfg[$key][1] }} me-50"></i>
                                    {{ $actions_cfg[$key][0] }}
                                </a>

                        @endswitch

                    @endforeach

                </div>

            @endif
            {{-- Дроп список - Если больше чем 2 --}}

            {{-- Кнопки - Если меньше или равно 2  --}}
            @php(($count <= 2) ? $slice = 2 : $slice = 1)
            @if($count)
                @foreach(array_slice($actions, 0, $slice) as $key => $action)

                    @switch($marker($key))

                        @case('destroy')
                            <button class="btn btn-sm btn-outline-secondary" type="submit"
                                    title="{{ $actions_cfg[$key][0] }}"
                                    data-bs-original-title="{{ $actions_cfg[$key][0] }}"
                                    data-bs-toggle="tooltip"
                                    @if($tooltip ?? false) data-bs-placement="{{ $tooltip }}" @endif
                            >
                                <i class="feather-trash"></i>
                            </button>
                            @break

                        @case('custom')
                            <a href="{{ $actions[$key]['url'] }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-secondary"
                               title="{{ $actions[$key]['text'] ?? '' }}"
                               data-bs-original-title="{{ $actions[$key]['text'] ?? '' }}"
                               data-bs-toggle="tooltip"
                               data-bs-placement="{{ ($tooltip ?? false) ? $tooltip : 'left' }}"
                            >
                                <i class="feather-{{ $actions[$key]['icon'] }}"></i>
                            </a>
                            @break

                        @case('html')
                            {!! view('grid::layouts.actions.html', [
                                'type' => 'btn',
                                'html' => $action
                            ])->render() !!}
                            @break

                        @case('print')
                            <a href="{{ $actions[$key] }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-secondary"
                               title="{{ $actions_cfg[$key][0] }}"
                            >
                                <i class="feather-{{ $actions_cfg[$key][1] }}"></i>
                            </a>
                            @break

                        @case('export')
                            @break

                            {{-- show, edit, destroy --}}
                        @default
                            <a href="{{ (is_bool($actions[$key])) ? route('admin.'. $module .'.'. $key, $id) : $actions[$key] }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-secondary"
                               title="{{ $actions_cfg[$key][0] }}"
                               data-bs-original-title="{{ $actions_cfg[$key][0] }}"
                               data-bs-toggle="tooltip"
                               data-bs-placement="{{ ($tooltip ?? false) ? $tooltip : 'left' }}"
                            >
                                <i class="feather-{{ $actions_cfg[$key][1] }}"></i>
                            </a>
                    @endswitch

                @endforeach
            @endif
            {{-- Кнопки - Если меньше или равно 2  --}}

        </div>

        @if($destroy ?? false)</form>
@endif
{{-- Автоматический actions генератор --}}

{{-- ЯВНО СКРИПТЫ НЕ ТУТ!!! --}}
{{--@section('vendor-script')--}}
{{--    <!-- vendor files -->--}}
{{--    <script src="{{ asset(mix('vendors/js/extensions/jquery.contextMenu.min.js')) }}"></script>--}}
{{--    <script src="{{ asset(mix('vendors/js/extensions/jquery.ui.position.min.js')) }}"></script>--}}
{{--@endsection--}}
{{--@section('page-script')--}}
{{--    <!-- Page js files -->--}}
{{--    <script src="{{ asset(mix('js/scripts/extensions/ext-component-context-menu.js')) }}"></script>--}}
{{--@endsection--}}