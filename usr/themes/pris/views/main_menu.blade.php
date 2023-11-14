{{-- Управление

menu_view - Меню Показано/Скрыто (по умолчанию false)

--}}

<div class="header-bottom noselect">
    <div class="container container-block">
        <div class="row">

            {{-- Меню - Левое --}}
            <div id="left_menu"
                 class="primary-menu-left vertical_megamenu vertical_megamenu-header verticle-menu-index pull-left @if($menu_view ?? false) mega-left-title-yes-home @endif">

                <div class="mega-left-title">
                    <strong>
                        @lang('app.menu_cats')
                    </strong>
                </div>

                <div class="vc_wp_custommenu wpb_content_element">
                    <div class="wrapper_vertical_menu vertical_megamenu">

                        <ul id="menu-menu-vertical-category2-1" class="vertical-megamenu home-menu-slider">

                            {{----}}
                            @foreach($menu as $cat)
                                <li class="dropdown menu-smartphone flytheme-menu-custom">

                                    @if(!$cat->parent_id)

                                        <a class="item-link dropdown-toggle"
                                           data-id="{{ $cat->id }}"
                                           href="{{ url($cat->module, $cat->altname) }}">
                                            <span class="have-title">
                                                <span class="menu-title">
                                                    {{ $cat->name }}
                                                </span>
                                            </span>
                                        </a>

                                    @endif

                                    <ul class="dropdown-menu">
                                        @foreach($cat_full as $cat_p)
                                            @if($cat->id == $cat_p->parent_id)
                                                <li class="dropdown-submenu">

                                                    <a href="{{ url($cat_p->module, $cat_p->altname) }}">
                                                        {{ $cat_p->name }}
                                                    </a>

                                                    <ul class="dropdown-sub">
                                                        @foreach($cat_full as $cat_p_p)
                                                            @if($cat_p->id == $cat_p_p->parent_id)

                                                                <li>
                                                                    <a href="{{ url($cat_p_p->module, $cat_p_p->altname) }}">
                                                                        {{ $cat_p_p->name }}
                                                                    </a>
                                                                </li>

                                                            @endif
                                                        @endforeach
                                                    </ul>

                                                </li>
                                            @endif
                                        @endforeach
                                        {{---}}
                                        <li class=" column-1 ">
                                            <a class="" href="catalog_v2/zip_i_rasodnye_materialy">ЗИП и расходные
                                                материалы</a>
                                        </li>
                                        {{----}}
                                    </ul>

                                </li>
                            @endforeach

                            <li class="dropdown menu-smartphone flytheme-menu-custom level1">
                                <a class="menu-link" href="{{ url('catalog_v2') }}">
                                    <span class="icon_items"></span>
                                    <span class="menu-title">
                                        Посмотреть все
                                    </span>
                                </a>
                            </li>
                            {{----}}

                        </ul>

                    </div>
                </div>
            </div>
            {{-- Меню - Левое --}}

            {{-- Меню - Горизонтальное --}}
            <div class="primary-menu-right primary-menu-home1 pull-left">
                <div id="main-menu" class="main-menu">
                    <nav id="primary-menu" class="primary-menu">
                        <div class="container container-block p-0">
                            <div class="mid-header clearfix">
                                <div class="navbar-inner navbar-inverse">
                                    <div class="resmenu-container">
                                        <div id="ResMenuprimary_menu" class="collapse menu-responsive-wrapper">
                                            <ul id="menu-primary-menu" class="flytheme_resmenu"></ul>
                                        </div>
                                    </div>
                                    <ul id="menu-primary-menu-1"
                                        class="nav menu-slinky nav-pills nav-mega flytheme-mega flytheme-menures">

                                        <li class="home-fix menu-home flytheme-mega-menu level1 flytheme-menu-icon {{ request()->is('/') ? 'active' : '' }}">
                                            <a href="{{ url('/') }}" class="item-link">
                                                <span class="have-icon have-title">
                                                    <span class="fa fa fa-home"></span>
                                                    <span class="menu-title">
                                                        Главная
                                                    </span>
                                                </span>
                                            </a>
                                        </li>

                                        <li class="menu-shop flytheme-mega-menu level1 menu-top-cat {{ request()->is('/catalog_v2') ? 'active' : '' }}">
                                            <a href="/catalog_v2" class="item-link">
                                                <span class="have-title">
                                                    <span class="menu-title">
                                                        Каталог
                                                    </span>
                                                </span>
                                            </a>
                                        </li>

                                        <li class="menu-blog flytheme-mega-menu level1 drop-about drop-about-company {{ request()->is(
                                            // Массив URI тут
                                            'news',
                                            'news/*',
                                            'reviews',
                                            'reviews/*',
                                            'documents',
                                            'documents/*',
                                            'certificates',
                                            'certificates/*',
                                            'portfolio',
                                            'portfolio/*',
                                            'partner',
                                            'partner/*'
                                            ) ? 'active' : '' }}">
                                            <a href="{{ url('page/about') }}" class="item-link">
                                                <span class="have-title">
                                                    <span class="menu-title">
                                                        О компании
                                                    </span>
                                                </span>
                                            </a>
                                            <ul class="dropdown-menu nav-level1 column-1">
                                                <li class="menu-left-sidebar-grid {{ request()->is('news', 'news*') ? 'active' : '' }}">
                                                    <a href="{{ url('news') }}">
                                                        <span class="have-title">
                                                            <i class="fad fa-newspaper"></i>
                                                            <span class="menu-title">
                                                                Новости и статьи
                                                            </span>
                                                        </span>
                                                    </a>
                                                </li>

                                                <li class="menu-right-sidebar-grid {{ request()->is('reviews', 'reviews/*') ? 'active' : '' }}">
                                                    <a href="{{ url('reviews') }}">
                                                        <span class="have-title">
                                                            <i class="fad fa-comments"></i>
                                                            <span class="menu-title">
                                                                Отзывы клиентов
                                                            </span>
                                                        </span>
                                                    </a>
                                                </li>

                                                <li class="menu-full-width-grid menu-doc {{ request()->is('documents', 'documents/*') ? 'active' : '' }}">
                                                    <a href="{{ url('content/documents.html') }}">
                                                        <i class="fad fa-file-alt"></i>
                                                        <span class="have-title">
                                                            <span class="menu-title">
                                                                Документы
                                                            </span>
                                                        </span>
                                                    </a>
                                                </li>

                                                <li class="menu-left-sidebar-list {{ request()->is('certificates', 'certificates/*') ? 'active' : '' }}">
                                                    <a href="{{ url('certificates') }}">
                                                        <i class="fad fa-address-card"></i>
                                                        <span class="have-title">
                                                            <span class="menu-title">
                                                                Сертификаты
                                                            </span>
                                                        </span>
                                                    </a>
                                                </li>

                                                <li class="menu-right-sidebar-list menu-object {{ request()->is('portfolio', 'portfolio/*') ? 'active' : '' }}">
                                                    <a href="{{ url('portfolio') }}">
                                                        <span class="have-title">
                                                            <i class="fad fa-building"></i>
                                                            <span class="menu-title">
                                                                Наши объекты
                                                            </span>
                                                        </span>
                                                    </a>
                                                </li>

                                                <li class="menu-right-sidebar-list menu-partner {{ request()->is('partner', 'partner/*') ? 'active' : '' }}">
                                                    <a href="{{ url('partner') }}">
                                                        <span class="have-title">
                                                            <i class="fad fa-handshake"></i>
                                                            <span class="menu-title">
                                                                Наши партнёры
                                                            </span>
                                                        </span>
                                                    </a>
                                                </li>

                                            </ul>
                                        </li>

                                        <li class="menu-blog flytheme-mega-menu level1 drop-about {{ request()->is('project', 'project/*') ? 'active' : '' }}">
                                            <a href="{{ url('project') }}" class="item-link">
                                                <span class="have-title">
                                                    <span class="menu-title">
                                                        Проект
                                                    </span>
                                                </span>
                                            </a>
                                            <ul class="dropdown-menu nav-level1 column-4">

                                                <li class="dropdown-submenu menu-blog-layouts-1">
                                                    <a href="javascript:void(0);" class="have-title-proekt">
                                                        <span class="have-title">
                                                            <span class="menu-title">
                                                                Проектирование
                                                            </span>
                                                        </span>
                                                    </a>
                                                    <ul class="dropdown-sub nav-level2">

                                                        <li class="menu-left-sidebar-grid ">
                                                            <a href="{{ url('project') }}">
                                                                <span class="have-title">
                                                                    <span class="menu-title">
                                                                        Общая информация
                                                                    </span>
                                                                </span>
                                                            </a>
                                                        </li>

                                                        <li class="menu-right-sidebar-grid ">
                                                            <a href="{{ url('project/order') }}">
                                                                <span class="have-title">
                                                                    <span class="menu-title">
                                                                        Подать заявку
                                                                    </span>
                                                                </span>
                                                            </a>
                                                        </li>

                                                    </ul>
                                                </li>

                                                <li class="dropdown-submenu menu-blog-layouts-2">
                                                    <a href="javascript:void(0);" class="have-title-proekt">
                                                        <span class="have-title">
                                                            <span class="menu-title">
                                                                Расчеты
                                                            </span>
                                                        </span>
                                                    </a>
                                                    <ul class="dropdown-sub nav-level2">
                                                        <li class="menu-2-columns-grid ">
                                                            <a href="{{ url('project/calculation/pump') }}">
                                                                <span class="have-title">
                                                                    <span class="menu-title">
                                                                        Подбор насосного оборудования
                                                                    </span>
                                                                </span>
                                                            </a>
                                                        </li>

                                                        <li class="menu-3-columns-grid ">
                                                            <a href="{{ url('project/calculation/block') }}">
                                                                <span class="have-title">
                                                                    <span class="menu-title">
                                                                        Расчёт блочного теплового пункта
                                                                    </span>
                                                                </span>
                                                            </a>
                                                        </li>

                                                        <li class="menu-4-columns-grid ">
                                                            <a href="{{ url('project/calculation/heat') }}">
                                                                <span class="have-title">
                                                                    <span class="menu-title">
                                                                        Подбор теплообменного оборудования
                                                                    </span>
                                                                </span>
                                                            </a>
                                                        </li>

                                                        <li class="menu-5-columns-grid ">
                                                            <a href="{{ url('project/calculation') }}">
                                                                <span class="have-title">
                                                                    <span class="menu-title">
                                                                        Все расчеты и подборы
                                                                    </span>
                                                                </span>
                                                            </a>
                                                        </li>

                                                    </ul>
                                                </li>

                                                <li class="dropdown-submenu menu-post-format">
                                                    <a href="javascript:void(0);" class="have-title-proekt">
                                                        <span class="have-title">
                                                            <span class="menu-title">
                                                                Инструменты
                                                            </span>
                                                        </span>
                                                    </a>
                                                    <ul class="dropdown-sub nav-level2">

                                                        <li class="menu-post-format-video ">
                                                            <a href="{{ url('project/tool/pipes') }}">
                                                                <span class="have-title">
                                                                    <span class="menu-title">
                                                                        Таблица Шевелева
                                                                    </span>
                                                                </span>
                                                            </a>
                                                        </li>

                                                        <li class="menu-post-format-audio ">
                                                            <a href="{{ url('project/tool/power') }}">
                                                                <span class="have-title">
                                                                    <span class="menu-title">
                                                                        Перевод единиц мощности
                                                                    </span>
                                                                </span>
                                                            </a>
                                                        </li>

                                                        <li class="menu-post-format-image ">
                                                            <a href="{{ url('project/tool/scales') }}">
                                                                <span class="have-title">
                                                                    <span class="menu-title">
                                                                        Перевод массы в количество
                                                                    </span>
                                                                </span>
                                                            </a>
                                                        </li>

                                                        <li class="menu-post-format-quote ">
                                                            <a href="{{ url('project/tool') }}">
                                                                <span class="have-title">
                                                                    <span class="menu-title">
                                                                        Все инструменты
                                                                    </span>
                                                                </span>
                                                            </a>
                                                        </li>

                                                    </ul>
                                                </li>

                                                <li class="dropdown-submenu menu-post-layouts">
                                                    <a href="javascript:void(0);" class="have-title-proekt">
                                                        <span class="have-title">
                                                            <span class="menu-title">
                                                                Документация
                                                            </span>
                                                        </span>
                                                    </a>
                                                    <ul class="dropdown-sub nav-level2">

                                                        <li class="menu-full-width">
                                                            <a href="{{ url('project/documents/gost') }}">
                                                                <span class="have-title">
                                                                    <span class="menu-title">
                                                                        ГОСТЫ
                                                                    </span>
                                                                </span>
                                                            </a>
                                                        </li>

                                                        <li class="menu-left-sidebar">
                                                            <a href="{{ url('project/documents/snip') }}">
                                                                <span class="have-title">
                                                                    <span class="menu-title">
                                                                        СНИПЫ
                                                                    </span>
                                                                </span>
                                                            </a>
                                                        </li>

                                                        <li class="menu-right-sidebar">
                                                            <a href="{{ url('certificates/other_cert') }}">
                                                                <span class="have-title">
                                                                    <span class="menu-title">
                                                                        Лицензии
                                                                    </span>
                                                                </span>
                                                            </a>
                                                        </li>

                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>

                                        <li class="menu-accessories flytheme-mega-menu level1 {{ request()->is('page/mounting') ? 'active' : '' }}">
                                            <a href="{{ url('page/mounting') }}" class="item-link ">
                                                <span class="have-title">
                                                    <span class="menu-title">
                                                        Монтаж
                                                    </span>
                                                </span>
                                            </a>
                                        </li>

                                        <li class="menu-accessories flytheme-mega-menu level1 {{ request()->is('page/servicing') ? 'active' : '' }}">
                                            <a href="{{ url('page/servicing') }}" class="item-link ">
                                                <span class="have-title">
                                                    <span class="menu-title">
                                                        Обслуживание
                                                    </span>
                                                </span>
                                            </a>
                                        </li>

                                        <li class="menu-accessories flytheme-mega-menu level1 {{ request()->is('service') ? 'active' : '' }}">
                                            <a href="{{ url('service') }}" class="item-link">
                                                <span class="have-title">
                                                    <span class="menu-title">
                                                        Сервис
                                                    </span>
                                                </span>
                                            </a>
                                        </li>

                                        <li class="menu-accessories flytheme-mega-menu level1 ml-auto {{ request()->is('feedback') ? 'active' : '' }}">
                                            <a href="{{ url('feedback') }}" class="item-link">
                                                <span class="have-title">
                                                    <span class="menu-title">
                                                        Контакты
                                                    </span>
                                                </span>
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
            {{-- Меню - Горизонтальное --}}

        </div>
    </div>
</div>

<script type="application/javascript">

    $('.vertical-megamenu').find('ul').each(function () {
        let txt = $("li", this).text();
        if (txt.length <= 0) {
            $(this).hide().parent().addClass('no_after');
        }
    });

    let left_m = $('#left_menu');
    $(left_m).on("click", function () {
        $(this).toggleClass('mega-left-title-yes-home');
    });

</script>