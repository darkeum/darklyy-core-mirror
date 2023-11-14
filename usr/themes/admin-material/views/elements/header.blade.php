{{-- # ИСПОЛЬЗОВАНИЕ АТОМАТИЧЕСКОГО HEADER --}}
{{--


--}}

{{-- Автоматический генератор header --}}
<div class="content-header row mb-1">

    {{-- TITLE - LEFT --}}
    <div class="content-header-left col-md-10 col-12 mb-50">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-start mb-0">
                    @if (isset($title))
                        {{ $title }}
                    @else
                        {{ trans(Request::segment(3) . '::lang.module') }}
                    @endif
                </h2>
                <div class="breadcrumb-wrapper">
                    @if (isset($deck))
                        {{ $deck }}
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- TITLE - LEFT --}}

    {{-- Кнопка справа - Как правило это ADD --}}
    <div class="content-header-right text-md-end col-md-2 col-12 d-block mb-50">
        <div class="breadcrumb-right text-end">

            @if (isset($add))

                @if (is_array($add[0]))

                    @php
                        $first = array_shift($add);
                    @endphp

                    <div class="btn-group dropdown dropend w-100 w-md-auto">
                        <button type="button" class="btn btn-sm btn-success waves-effect"
                           href="{{ Route::has($first[1]) ? route($first[1]) : $first[1] }}">
                            {{ $first[0] }}
                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-success dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                        </button>
                        <div class="dropdown-menu">

                            @foreach($add as $key => $drop)
                                <a class="dropdown-item"
                                   href="{{ Route::has($drop[1]) ? route($drop[1]) : $drop[1] }}"
                                >
                                    {{ $drop[0] }}
                                </a>
                            @endforeach

                        </div>
                    </div>

                @else
                    <a class="btn btn-sm btn-success waves-effect w-100 w-md-auto"
                       href="{{ Route::has($add[1]) ? route($add[1]) : $add[1] }}">
                        {{ $add[0] }}
                    </a>
                @endif
            @endif

        </div>
    </div>
    {{-- Кнопка справа - Как правило это ADD --}}

    {{-- Меню модуля --}}
    <div class="row">
        <div class="d-inline-block">

            {{-- Перебор кнопок --}}
            @foreach (collect($buttons) as $key => $button)

                {{-- Вывод дроп списка или нет --}}
                @if (is_array($button))

                    <div class="btn-group mb-25" role="group" aria-label="Группа в меню">

                        @if($key == 'group')

                            @foreach ($button as $key => $opt)
                                <a class="btn btn-sm {{ request()->routeIs($opt) ? 'btn-primary' : 'btn-outline-primary' }}"
                                   href="{{ Route::has($opt) ? route($opt) : $opt }}">
                                    {{ $key }}
                                </a>
                            @endforeach

                        @else

                            {{-- Dropdown Menu --}}
                            <button
                                class="btn btn-sm dropdown-toggle waves-effect {{ stripos(json_encode($button, JSON_UNESCAPED_UNICODE), request()->route()->getName())? 'btn-primary': 'btn-outline-primary' }}"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                                {{ $key }}
                            </button>
                            <div class="dropdown-menu">
                                @foreach ($button as $key => $opt)
                                    <a class="dropdown-item {{ request()->routeIs($opt) ? 'active' : '' }}"
                                       href="{{ Route::has($opt) ? route($opt) : $opt }}">
                                        {{ $key }}
                                    </a>
                                @endforeach
                            </div>
                            {{-- Dropdown Menu --}}

                        @endif
                    </div>

                @else

                    <a class="btn btn-sm waves-effect mb-25 {{ request()->routeIs($button) ? 'btn-primary' : 'btn-outline-primary' }}"
                       href="{{ Route::has($button) ? route($button) : $button }}">
                        {{ $key }}
                    </a>

                @endif
                {{-- Вывод дроп списка или нет --}}

            @endforeach
            {{-- Перебор кнопок --}}

            {{-- Последняя кнопка - Индикатор редактирования и показа --}}
            @php
                $check = explode('@', Route::currentRouteAction());
                $segments = collect(Request::segments());
            @endphp

            @switch($check[1] ?? false)
                @case('edit')
                <a class="btn btn-sm p-0 mb-25"><i class="feather-chevron-right"></i></a>
                <a class="btn btn-sm mb-25 p-0">
                    Редактирование #{{ $segments->slice(0, -1)->last() }}
                </a>
                @break

                @case('show')
                <a class="btn btn-sm p-0 mb-25"><i class="feather-chevron-right"></i></a>
                <a class="btn btn-sm mb-25 p-0">
                    Просмотр #{{ $segments->last() }}
                </a>
                @break

                @default
                {{--  --}}
            @endswitch
            {{-- Последняя кнопка - Индикатор редактирования и показа --}}

        </div>
    </div>
    {{-- Меню модуля --}}

</div>
{{-- Автоматический генератор header --}}