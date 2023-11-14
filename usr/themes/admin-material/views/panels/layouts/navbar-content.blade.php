<div class="navbar-container d-flex content">

    {{-- Панель закладок --}}
    <livewire:bookmarks/>
    {{--/ Панель закладок --}}

    <ul class="nav navbar-nav align-items-center ms-auto">

        {{-- Курс валют --}}
        <li class="nav-item d-none d-xxl-flex me-50">
            <ul class="nav navbar-nav align-items-center border align-middle">

                @php
                    // $price_us_dis = 1.0000;
                    // $price_eur_dis = -21.0000;
                @endphp

                {{-- Курс валют --}}
                {{-- Доллар --}}
                <li class="nav-item border-end border-2">
                    <span class="px-50" title="">
                        <span class="badge text-success fs-4">$</span>
                        <span class="border-end ps-25 pe-50 text-center">{!! $price_us !!}</span>
                        <span
                            class="{{ $price_us_dis >= 0 ? ($price_us_dis > 0) ? 'text-success' : 'text-secondary' : 'text-danger' }}">
                            @switch($price_us_dis)

                                @case($price_us_dis > 0 and $price_us_dis < 3)
                                <i class="feather-chevron-up"></i>
                                @break

                                @case($price_us_dis >= 3)
                                <i class="feather-chevrons-up"></i>
                                @break

                                @case($price_us_dis < 0 and $price_us_dis > -3)
                                <i class="feather-chevron-down"></i>
                                @break

                                @case($price_us_dis <= -3)
                                <i class="feather-chevrons-down"></i>
                                @break

                            @endswitch
                            <small>{!! bcdiv($price_us_dis, 1, 4) !!}</small>
                        </span>
                    </span>
                </li>
                {{--/ Доллар --}}
                {{-- Евро --}}
                <li class="nav-item">
                    <span class="px-50" title="">
                        <span class="badge text-info fs-4">€</span>
                        <span class="border-end ps-25 pe-50">{!! $price_eur !!}</span>
                        <span
                            class="{{ $price_eur_dis >= 0 ? ($price_eur_dis > 0) ? 'text-success' : 'text-secondary' : 'text-danger' }}">
                            @switch($price_eur_dis)

                                @case($price_eur_dis > 0 and $price_eur_dis < 3)
                                <i class="feather-chevron-up"></i>
                                @break

                                @case($price_eur_dis >= 3)
                                <i class="feather-chevrons-up"></i>
                                @break

                                @case($price_eur_dis < 0 and $price_eur_dis > -3)
                                <i class="feather-chevron-down"></i>
                                @break

                                @case($price_eur_dis <= -3)
                                <i class="feather-chevrons-down"></i>
                                @break

                            @endswitch
                            <small>{!! bcdiv($price_eur_dis, 1, 4) !!}</small>
                        </span>
                    </span>
                </li>
                {{--/ Евро --}}
                {{--/ Курс валют --}}

            </ul>
        </li>

        <li class="nav-item dropdown d-xxl-none">
            <a class="nav-link dropdown-toggle"
               id="dropdown-currency"
               data-bs-toggle="dropdown"
               aria-haspopup="true"
            >
                <span class="d-block" title="Курсы валют" data-bs-toggle="tooltip"
                      data-bs-placement="bottom">
                    <span class="badge text-success fs-4">$</span>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-currency">
                <a class="dropdown-item p-0">
                    <span class="px-50" title="">
                        <span class="badge text-success fs-4">$</span>
                        <span class="border-end ps-25 pe-50 text-center">{!! $price_us !!}</span>
                        <span
                            class="{{ $price_us_dis >= 0 ? ($price_us_dis > 0) ? 'text-success' : 'text-secondary' : 'text-danger' }}">
                            @switch($price_us_dis)

                                @case($price_us_dis > 0 and $price_us_dis < 3)
                                <i class="feather-chevron-up"></i>
                                @break

                                @case($price_us_dis >= 3)
                                <i class="feather-chevrons-up"></i>
                                @break

                                @case($price_us_dis < 0 and $price_us_dis > -3)
                                <i class="feather-chevron-down"></i>
                                @break

                                @case($price_us_dis <= -3)
                                <i class="feather-chevrons-down"></i>
                                @break

                            @endswitch
                            <small>{!! bcdiv($price_us_dis, 1, 4) !!}</small>
                        </span>
                    </span>
                </a>
                <a class="dropdown-item p-0">
                    <span class="px-50" title="">
                        <span class="badge text-info fs-4">€</span>
                        <span class="border-end ps-25 pe-50">{!! $price_eur !!}</span>
                        <span
                            class="{{ $price_eur_dis >= 0 ? ($price_eur_dis > 0) ? 'text-success' : 'text-secondary' : 'text-danger' }}">
                            @switch($price_eur_dis)

                                @case($price_eur_dis > 0 and $price_eur_dis < 3)
                                <i class="feather-chevron-up"></i>
                                @break

                                @case($price_eur_dis >= 3)
                                <i class="feather-chevrons-up"></i>
                                @break

                                @case($price_eur_dis < 0 and $price_eur_dis > -3)
                                <i class="feather-chevron-down"></i>
                                @break

                                @case($price_eur_dis <= -3)
                                <i class="feather-chevrons-down"></i>
                                @break

                            @endswitch
                            <small>{!! bcdiv($price_eur_dis, 1, 4) !!}</small>
                        </span>
                    </span>
                </a>
            </div>
        </li>
        {{--/ Курс валют --}}

        {{-- Смена языка --}}
        <li class="nav-item dropdown dropdown-language">
            <a class="nav-link dropdown-toggle"
               id="dropdown-flag"
               data-bs-toggle="dropdown"
               aria-haspopup="true"
            >
                <span class="d-block" title="@lang('app.lang.title')" data-bs-toggle="tooltip"
                      data-bs-placement="bottom">
                    <i class="flag-icon flag-icon-us"></i>
                    <span class="selected-language">
                        @lang('app.lang.ru')
                    </span>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-flag">
                <a class="dropdown-item" href="{{ url('lang/ru') }}" data-language="ru">
                    <i class="flag-icon flag-icon-ru"></i> @lang('app.lang.ru')
                </a>
                <a class="dropdown-item" href="{{ url('lang/en') }}" data-language="en">
                    <i class="flag-icon flag-icon-us"></i> @lang('app.lang.en')
                </a>

            </div>
        </li>
        {{--/ Смена языка --}}

        {{-- Смена цвета сайта --}}
        <livewire:theme-mod/>
        {{--/ Смена цвета сайта --}}

        {{-- Поиск --}}
        <li class="nav-item nav-search">
            <a class="nav-link nav-link-search"
               title="@lang('app.search.title')"
               data-bs-toggle="tooltip"
               data-bs-placement="bottom"
            >
                <i class="fa-light fa-search fa-xl"></i>
            </a>
            <div class="search-input">
                <div class="search-input-icon"><i data-feather="search"></i></div>
                <input class="form-control input"
                       type="text"
                       placeholder="@lang('admin.search')..."
                       tabindex="-1"
                       data-search="search"
                       data-search-text="@lang('admin.search.results')"
                >
                <div class="search-input-close">
                    <i data-feather="x"></i>
                </div>

                {{-- Результаты поиска --}}
                <ul class="search-list search-list-main"></ul>
                {{--/ Результаты поиска --}}

                {{-- Параметры поиска --}}
                <div class="search-options">

                    <div
                        class="search-options-btn"
                        id="search-options"
                        data-bs-original-title="@lang('admin.search.settings')"
                        data-bs-offset="-100,10"
                        data-bs-trigger="click"
                    >
                        <a>
                            {{--<i data-feather="settings"></i>--}}
                            <i class="feather-settings fa-xl"></i>
                            <span>@lang('admin.search.settings.tooltip')</span>
                            {{--<div class="badge bg-primary badge-up">6</div>--}}
                        </a>
                    </div>

                </div>
                {{--/ Параметры поиска --}}

            </div>
        </li>
        {{--/ Поиск --}}

        {{-- Уведомления --}}
        <livewire:notifications/>
        {{--/ Уведомления --}}

        {{-- Профиль --}}
        <li class="nav-item dropdown dropdown-user">
            <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user"
               data-bs-toggle="dropdown" aria-haspopup="true">
                <div class="user-nav d-sm-flex d-none">
                    <span class="user-name fw-bolder" title="{{ $user->full_user_name }}">
                        {{ $user->surname }} {{ $user->name }}
                    </span>
                    <span class="user-status">
                        {{--{{ $user->roles->first()['lang_' . app()->getLocale()] }}--}}
                        {{ trans('roles::lang.roles')[$user->roles->first()->name] }}
                    </span>
                </div>

                <span class="avatar">
                    <img class="round" src="{{ $user->avatar() }}" alt="avatar" height="40" width="40">
                    <span class="avatar-status-online"></span>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                <h6 class="dropdown-header">@lang('admin.profile.manager')</h6>
                <div class="dropdown-divider"></div>

                {{-- Профиль --}}
                <a class="dropdown-item"
                   href="{{ Route::has('admin.profile.index') ? route('admin.profile.index') : 'javascript:void(0)' }}">
                    <i class="me-50" data-feather="user"></i> @lang('admin.profile')
                </a>
                {{-- Профиль --}}

                {{-- Уведомления --}}
                <a class="dropdown-item"
                   href="{{ Route::has('admin.notifications.index') ? route('admin.notifications.index') : 'javascript:void(0)' }}">
                    <i class="me-50" data-feather="bell"></i> @lang('admin.notifications')
                    {{--<span class="badge rounded-pill bg-danger ms-auto">5</span>--}}
                </a>
                {{-- Уведомления --}}

                {{-- Настройки --}}
                <a class="dropdown-item" href="{{ route('admin.profile.settings') }}">
                    <i class="me-50" data-feather="settings"></i> @lang('admin.settings')
                </a>
                {{-- Настройки --}}

                {{-- Выход --}}
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="me-50" data-feather="power"></i> @lang('admin.logout')
                </a>
                {{-- Выход --}}

                <form method="POST" id="logout-form" action="{{ route('logout') }}">
                    @csrf
                </form>
            </div>
        </li>
        {{--/ Профиль --}}

    </ul>

</div>

@push('scripts')
    <script>
        // $('.dropdown-language').tooltip();
        // $(".dropdown-language").dropdown();
    </script>
@endpush