<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" xml:lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#535a6c">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport"
          content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta http-equiv="Content-Language" content="{{ app()->getLocale() }}"/>
    {{-- Favicon --}}
    <link rel="icon" href="{{ $manager_theme }}/assets/images/favicon.ico" type="image/x-icon">
    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Stylesheets --}}
    <link rel="stylesheet" type="text/css"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
          crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="{{ $manager_theme }}/assets/css/informers.css">
    <link rel="stylesheet" type="text/css"
          href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ $manager_admin }}/assets/css/select2-bootstrap-5-theme.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ $manager_admin }}/assets/css/admin-theme.css">
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="{{ $manager_theme }}/assets/css/admin-forms.css">
    <link rel="stylesheet" type="text/css" href="{{ $manager_admin }}/assets/css/styles.css">
    <link rel="stylesheet" type="text/css" href="{{ $manager_admin }}/assets/css/dark-editable.css">
    {{-- {% for link in chat_data.link %}
        {{link|raw}}
    {% endfor %} --}}
    <title>
        @hasSection('title') @yield('title') - @endif
        Корпоративный портал
    </title>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
            integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
            integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
            crossorigin="anonymous"></script>
    <script src="{{ $manager_theme }}/assets/js/jquery.min.js"></script>
    <script src="{{ $manager_theme }}/assets/js/jquery-migrate-301.min.js"></script>
    <script src="{{ $manager_theme }}/assets/js/jquery-ui.js"></script>
    <script src="/usr/plugins/js/js.cookie.js"></script>
    <script src="{{ $manager_theme }}/assets/js/core.min.js"></script>
    <script src="{{ $manager_theme }}/assets/js/utility.js"></script>
    <script src="{{ $manager_theme }}/assets/js/offline.min.js"></script>
    <script src="/usr/tpl/corp/assets/js/script.js"></script>

    <script src="/usr/tpl/admin/assets/js/sweetalert2.all.min.js"></script>
    <script src="/usr/tpl/admin/assets/js/dark-editable.js"></script>
    <script src="/usr/other/sub/load_window/asset/js/load_window.js"></script>

    <script src="/usr/other/sub/price/js/price.js?1105"></script>

    <script src="/usr/tpl/admin/assets/js/vue.global.js"></script>
    <script src="/usr/tpl/admin/assets/js/admin-script.js"></script>
    {{-- Fonts --}}
    <link rel="stylesheet" type="text/css"
          href="//fonts.googleapis.com/css?family=Lato:300,400,600,700%7CMontserrat:400,700">
    <link rel="stylesheet" type="text/css" href="{{ $manager_theme }}/assets/fonts/font-awesome-pro/css/all.min.css">
    @stack('head')
    @stack('assets')
    @livewireStyles
</head>

<body data-spy="scroll" data-target="#nav-spy" data-offset="300" class="form-editors-page online {{ $body_class }} {{ $NOW_COMPANY }}">
<div class="overlay-offline"></div>
<div id="main">
    <header class="navbar navbar-fixed-top bg-dark justify-content-start align-items-start flex-nowrap">
        <div class="navbar-branding light bg-danger">
            <a href="{{ $manager }}" class="navbar-brand text-uppercase">
                <img style="width: 120px; padding-left: 5px;" src="{{ $manager_theme }}/assets/images/logo-{{ $NOW_COMPANY }}.png"
                     alt="logo">
            </a>
            <span id="toggle_sidemenu_l" class="fa fa-bars"></span>
        </div>
        <div class="d-flex justify-content-between flex-grow-1">
            <form role="search" method="POST" action="{{ $manager }}/search"
                  class="nophone navbar-form navbar-left navbar-search alt d-xl-flex d-none">
                <span class="hide visible-md-inline-block visible-lg-inline-block fa fa-search fs18"></span>
                <div class="form-group">
                    <input type="text" name="search" placeholder="Поиск" class="form-control">
                </div>
            </form>

            <div class="d-flex">
                <div class="price-bar d-lg-flex d-md-flex d-none">
                    <div class="priceContainer priceContainer-mr">
                        <div class="symbolContainer symbolContainer-mr" title="Доллар США">$</div>
                        <div class="amount_middle">
                            <div class="amount_middle__above">{{ $price_us }}</div>
                            <div class="amount_middle__under {{ $price_us_dis_color }}">{{ $price_us_dis }}</div>
                        </div>
                    </div>
                    <div class="priceContainer">
                        <div class="symbolContainer symbolContainer-mr" title="Евро">€</div>
                        <div class="amount_middle">
                            <div class="amount_middle__above">{{ $price_eur }}</div>
                            <div class="amount_middle__under {{ $price_eur_dis_color }}">{{ $price_eur_dis }}
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="nav">
                    <li class="nophone">
                        <div class="navbar-btn btn-group">
                            <a href="/" target="_blank" class="btn btn-lg" title="Перейти на сайт"><span
                                        class="fa fa-globe fs15"></span></a>
                        </div>
                    </li>
                    <li class="">
                        <div class="navbar-btn btn-group">
                            <a href="javascript:void(0);" class="btn btn-lg AddProduct" onclick="{{ $victoria_click }}"
                               title="Быстрый подбор">
                                <span class="fal fa-cart-arrow-down fs15"></span>
                            </a>
                        </div>
                    </li>
                    <li class="nophone">
                        <div class="navbar-btn btn-group">
                            <button class="btn btn-lg request-fullscreen" title="Во весь экран"><span
                                        class="fa fa-arrows-alt fs15"></span></button>
                        </div>
                    </li>
                    <li class="nophone">
                        <div class="navbar-btn btn-group">
                            <a href="#" class="topbar-menu-toggle btn btn-lg"><span class="fa fa-magic"></span></a>
                        </div>
                    </li>
                    <li class="dropdown menu-merge mr20">
                        <div class="navbar-btn btn-group">
                            <button data-toggle="dropdown" class="btn btn-lg dropdown-toggle">
                                <span class="fas fa-bell fs14 va-m"></span>
                                <span class="badge badge-danger">{{ $notif_numb }}</span>
                            </button>
                            <div role="menu"
                                 class="dropdown-menu dropdown-persist w350 animated animated-shorter fadeIn"
                                 style="left: -280px;">
                                <div class="panel mbn">
                                    <div class="panel-menu">
                                        <span class="panel-icon">
                                            <i class="fa fa-clock-o"></i>
                                        </span>
                                        <span class="panel-title fw600">
                                            Уведомления
                                        </span>
                                    </div>
                                    <div class="w100p panel-body panel-scroller scroller-navbar scroller-overlay scroller-pn pn">
                                        <ol class="timeline-list">
                                            {!! $notif !!}
                                        </ol>
                                    </div>
                                    <div class="panel-footer text-center p7">
                                        <a href="{{ $manager }}/module/notif" class="link-unstyled">
                                            Смотреть Все
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li><img width="70" height="70" src="/{{ $avatar }}" alt="avatar"></li>
                    <li class="dropdown menu-merge">
                        <a href="javascript:;" data-toggle="dropdown" class="fw600 p15">
                            <span class="fa fa-angle-down"></span>
                        </a>
                        <ul role="menu" class="dropdown-menu list-group dropdown-persist w250" style="left: -210px;">
                            <li class="dropdown-header clearfix">
                                <div class="pull-left ml10">
                                    <select id="user-status">
                                        <optgroup label="Статус">
                                            <option value="3">Всегда</option>
                                            <option value="2">Оффлайн</option>
                                            <option value="1">Онлайн</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="pull-right mr10">
                                    <div class="btn-group">
                                        <button type="button" class="multiselect dropdown-toggle btn btn-default btn-lg"
                                                data-toggle="dropdown" style="max-width: 100px;" aria-expanded="false">
                                            Профиль <b
                                                    class="caret-right"></b></button>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item"><a href="pm" class="animated animated-short fadeInUp"><span
                                            class="fa fa-envelope"></span>
                                    Сообщения<span class="label label-warning">{{ $messages_numb }}</ span></a>
                            </li>
                            <li class="list-group-item"><a href="profile" class="animated animated-short fadeInUp"><span
                                            class="fa fa-user"></span>
                                    Друзья<span class="label    label-warning">{{ $friends_numb }}</ span></a>
                            </li>
                            <li class="list-group-item"><a href="profile" class="animated animated-short fadeInUp"><span
                                            class="fa fa-bell"></span>
                                    Профиль</a></li>
                            <li class="list-group-item"><a href="{{ $manager }}/config"
                                                           class="animated animated-short fadeInUp"><span
                                            class="fa fa-gear"></span>
                                    Настройки</a></li>
                            <li class="dropdown-footer">
                                <a href="profile/logout"><span class="fa fa-power-off pr5"></span> Выход</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <aside id="sidebar_left" class="nano nano-light affix has-scrollbar sidebar-light">
        <div class="sidebar-left-content nano-content">
            <header class="sidebar-header">
                <div class="sidebar-widget search-widget hidden">
                    <div class="input-group"><span class="input-group-addon"><i class="fa fa-search"></i></span>
                        <input id="sidebar-search" type="text" placeholder="Search..." class="form-control"/>
                    </div>
                </div>
            </header>
            {{-- {!! Menu::main() !!} --}}
            <ul class="nav sidebar-menu">

                <li {{ menu_choose('main') }}>
                    <a href="{{ $manager }}"><span class="glyphicon glyphicon-home"></span><span class="sidebar-title">Главная</span></a>
                </li>
                <li {{ menu_choose('task') }}>
                    <a href="{{ $manager }}/module/task"><span class="glyphicon glyphicon-ok-sign"></span><span
                                class="sidebar-title">Задачи</span>
                        <span class="badge badge-danger">{{ $task_count }}</span></a>
                </li>
                <li>
                    <a href="javascript:;" class="accordion-toggle {{ menu_open('sales,realization,boiler,form') }}">
                        <span class="glyphicon glyphicon-shopping-cart"></span>
                        <span class="sidebar-title">Заказы</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {{ menu_choose_module('boiler', 'bid') }}>
                            <a href="{{ $manager }}/module/boiler/bid"><span class="fas fa-cauldron"></span>Котел</a>
                        </li>
                        <li {{ menu_choose('sales') }}>
                            <a href="{{ $manager }}/module/sales"><span class="fa fa-book"></span>Все
                                счета</a>
                        </li>
                        <li {{ menu_choose('realization') }}>
                            <a href="{{ $manager }}/module/realization"><span class="fa fa-rub"></span>Документы продажи</a>
                        </li>
                        <li {{ menu_choose_module('realization', 'correct') }}>
                            <a href="{{ $manager }}/module/realization/correct"><span class="far fa-edit"></span>Документы
                                корректировки</a>
                        </li>
                        <li {{ menu_choose('form') }}>
                            <a href="{{ $manager }}/module/form"><span class="fa fa-calculator"></span>Подбор
                                оборудования</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;"
                       class="accordion-toggle {{ menu_open('client,organizations,calls,order,select,profile') }}">
                        <span class="glyphicon glyphicon-user"></span>
                        <span class="sidebar-title">Работа с клиентами</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {{ menu_choose('calls') }}>
                            <a href="{{ $manager }}/calls"><span class="far fa-phone"></span>Звонки</a>
                        </li>
                        <li {{ menu_choose('order') }}>
                            <a href="{{ $manager }}/module/order"><span class="far fa-comment-alt-check"></span>Заявки и
                                обращения</a>
                        </li>
                        <li {{ menu_choose('organizations') }}>
                            <a href="{{ $manager }}/module/organizations"><span class="far fa-building"></span>Контрагенты</a>
                        </li>
                        <li {{ menu_choose('client') }}>
                            <a href="{{ $manager }}/client"><span class="far fa-users"></span>Контактные
                                лица</a>
                        </li>
                        <li {{ menu_choose('profile') }}>
                            <a href="{{ $manager }}/module/profile"><span class="far fa-gift"></span>Новогодние подарки</a>
                        </li>
                        <li {{ menu_choose('promotions') }}>
                            <a href="{{ $manager }}/module/promotions"><span class="far fa-gift"></span>Акции</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;" class="accordion-toggle {{ menu_open('supply,logistics') }}">
                        <span class="fa fa-truck"></span>
                        <span class="sidebar-title">Закупки</span>
                        <span class="caret"></span>
                        <span class="badge badge-danger">{{ $supply_count }}</span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {{ menu_choose_module('supply', 'main,detail') }}>
                            <a href="{{ $manager }}/module/supply"><span class="fa fa-check-square"></span>Заявки на
                                закупку <span class="badge badge-danger">{{ $supply_count_type_1 }}</span></a>
                        </li>
                        <li {{ menu_choose_module('supply', 'supply_stock') }}>
                            <a href="{{ $manager }}/module/supply/supply_stock"><span class="fa fa-check-square"></span>Заявки
                                на закупку(склад)</a>
                        </li>
                        <li {{ menu_choose_module('supply', 'twomain,twodetail') }}>
                            <a href="{{ $manager }}/module/supply/twomain"><span class="fa fa-clock-o"></span>Заявки на
                                сроки <span class="badge badge-danger">{{ $supply_count_type_2 }}</span></a>

                        </li>
                        <li {{ menu_choose_module('supply', 'order') }}>
                            <a href="{{ $manager }}/module/supply/order"><span class="far fa-file-text"></span>Счета
                                поставщиков</a>
                        </li>
                        <li {{ menu_choose('logistics') }}>
                            <a href="{{ $manager }}/module/logistics"><span class="fa fa-truck"></span>Логистика</a>
                        </li>
                        <li {{ menu_choose_module('supply', 'receipts') }}>
                            <a href="{{ $manager }}/module/supply/receipts"><span class="fa fa-book"></span>Документы
                                поступления</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;"
                       class="accordion-toggle {{ menu_open('stock,barcode,storage,merging,delivery_storage,') }}">
                        <span class="fa fa-archive"></span>
                        <span class="sidebar-title">Склад</span>
                        <span class="caret"></span>
                        <span class="badge badge-danger">{{ $storage_count }}</span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {{ menu_choose_module('storage', 'main') }}>
                            <a href="{{ $manager }}/module/storage"><span class="fa fa-dot-circle-o "></span>Заявки на
                                пересчет <span class="badge badge-danger">{{ $recount_count }}</span></a>
                        </li>
                        <li {{ menu_choose_module('storage', 'assembly') }}>
                            <a href="{{ $manager }}/module/storage/assembly"><span class="fas fa-box-open "></span>Заявки
                                на сборку <span class="badge badge-danger">{{ $assembly_count }}</span></a>
                        </li>
                        <li {{ menu_choose_module('storage', 'docPrint') }}>
                            <a href="{{ $manager }}/module/storage/docPrint"><span class="fal fa-ballot-check"></span>Разрешенная
                                отгрузка </a>
                        </li>
                        <li {{ menu_choose('merging') }}>
                            <a href="{{ $manager }}/module/merging"><span class="fal fa-box-check"></span>Комплектация
                                товаров</a>
                        </li>
                    <!-- <li {{ menu_choose_module('storage', 'reserv') }}>
      <a href="{{ $manager }}/module/storage/reserv"><span class="fas fa-lock-alt"></span>Менеджер резервов </a>
     </li>	 -->
                        <li {{ menu_choose_module('storage', 'transfer') }}>
                            <a href="{{ $manager }}/module/storage/transfer"><span class="fa fa-retweet"></span>Перемещение
                                товаров <span class="badge badge-danger">{{ $transfer_count }}</span></a>
                        </li>
                        <li {{ menu_choose('stock') }}>
                            <a href="{{ $manager }}/stock"><span class="fa fa-archive "></span>Наличие
                                товаров</a>
                        </li>
                        <li {{ menu_choose('barcode') }}>
                            <a href="{{ $manager }}/module/barcode"><span class="fa fa-qrcode"></span>Штрихкоды</a>
                        </li>
                        <li {{ menu_choose_module('storage', 'delivery_storage') }}>
                            <a href="{{ $manager }}/module/storage/delivery_storage"><span class="fa fa-truck"></span>Доставка</a>
                        </li>

                    </ul>
                </li>
                @if (check_access('1'))
                    <li>
                        <a href="javascript:;" class="accordion-toggle {{ menu_open('production') }}">
                            <span class="fas fa-industry-alt"></span>
                            <span class="sidebar-title">Производство</span>
                            <span class="caret"></span>
                        </a>
                        <ul class="nav sub-nav">
                            <li {{ menu_choose('production') }}>
                                <a href="{{ $manager }}/module/production"><span class="far fa-cabinet-filing"></span>Реестр
                                    изделий</a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if (check_access('9'))
                    <li>
                        <a href="javascript:;"
                           class="accordion-toggle {{ menu_open('request,selection,specification') }}">
                            <span class="fas fa-drafting-compass"></span>
                            <span class="sidebar-title">Проектирование</span>
                            <span class="caret"></span>
                        </a>
                        <ul class="nav sub-nav">
                            <li {{ menu_choose('request') }}>
                                <a href="{{ $manager }}/module/request"><span class="far fa-envelope-open-text"></span>Заявки
                                    на расчет</a>
                            </li>
                            <li {{ menu_choose_module('selection', 'request') }}>
                                <a href="{{ $manager }}/module/selection/request"><span class="fas fa-cubes"></span>Заявки
                                    на БТП</a>
                            </li>
                            <li {{ menu_choose_module('selection', 'main') }} {{ menu_choose_module('selection', 'detail') }}>
                                <a href="{{ $manager }}/module/selection"><span class="fas fa-box-open"></span>Блоки</a>
                            </li>
                            <li {{ menu_choose_module('selection', 'metering') }} {{ menu_choose_module('selection', 'metering_detail') }}>
                                <a href="{{ $manager }}/module/selection/metering"><span
                                            class="fas fa-tachometer-slowest"></span>Узлы учета</a>
                            </li>
                            <li {{ menu_choose_module('engineering', 'register') }} {{ menu_choose_module('engineering', 'register') }}>
                                <a href="{{ $manager }}/module/engineering/register"><span
                                            class="fas fa-tachometer-slowest"></span>Регистр</a>
                            </li>
                            <li {{ menu_choose('specification') }}>
                                <a href="{{ $manager }}/module/specification"><span
                                            class="fas fa-tachometer-slowest"></span>Спецификации</a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if (check_access('8'))
                    <li>
                        <a href="javascript:;" class="accordion-toggle {{ menu_open('fcontrol') }}">
                            <span class="glyphicon glyphicon-credit-card"></span>
                            <span class="sidebar-title">Оплата и контроль</span>
                            <span class="caret"></span>
                            <span class="badge badge-danger">{{ $fcontrol_all }}</span>
                        </a>
                        <ul class="nav sub-nav">
                            <li {{ menu_choose_module('fcontrol', 'main') }}>
                                <a href="{{ $manager }}/module/fcontrol"><span
                                            class="glyphicon glyphicon-ok-circle"></span>Контроль закупок <span
                                            class="badge badge-danger">{{ $fcontrol }}</span></a>
                            </li>
                            <li {{ menu_choose_module('fcontrol', 'storage') }}>
                                <a href="{{ $manager }}/module/fcontrol/storage"><span
                                            class="glyphicon glyphicon-random"></span>Контроль остатков</a>
                            </li>
                            <li {{ menu_choose_module('fcontrol', 'pay') }}>
                                <a href="{{ $manager }}/module/fcontrol/pay"><span
                                            class="glyphicon glyphicon-usd"></span>Заявки оплату <span
                                            class="badge badge-danger">{{ $fcontrol_pay }}</span></a>
                            </li>
                            <li {{ menu_choose('god') }}>
                                <a href="{{ $manager }}/god"><span class="fas fa-users-crown"></span>Управление
                                    остатками</a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if (check_access('7'))
                    <li {{ menu_choose('verification') }}>
                        <a href="{{ $manager }}/module/verification"><span class="fa fa-dashboard"></span><span
                                    class="sidebar-title">Поверка
                                    приборов</span></a>
                    </li>
                @endif
                <li {{ menu_choose('object') }}>
                    <a href="{{ $manager }}//object"><span class="glyphicon glyphicon-briefcase"></span><span
                                class="sidebar-title">Объекты</span></a>
                </li>

                <li>
                    <a href="javascript:;"
                       class="accordion-toggle {{ menu_open('catalog_v2,attribute,price,cats,groups') }}">
                        <span class="glyphicon glyphicon-th-large"></span>
                        <span class="sidebar-title">Каталог товаров</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {{ menu_choose_module('catalog_v2', 'main') }}>
                            <a href="{{ $manager }}/module/catalog_v2"><span class="fa fa-list"></span>Список
                                товаров</a>
                        </li>
                        <li {{ menu_choose_module('catalog_v2', 'cert') }}>
                            <a href="{{ $manager }}/module/catalog_v2/cert"><span
                                        class="glyphicon glyphicon-ok-circle"></span>Сертификаты товаров</a>
                        </li>
                        <li {{ menu_choose_module('price', 'main') }}>
                            <a href="{{ $manager }}/module/price"><span class="glyphicon glyphicon-usd"></span>Цены
                                номенклатуры</a>
                        </li>
                        <li {{ menu_choose('attribute') }}>
                            <a href="{{ $manager }}/attribute"><span class="fas fa-tags"></span>Редактор
                                атрибутов</a>
                        </li>
                        <li {{ menu_choose_module('catalog_v2', 'file_upload') }}>
                            <a href="{{ $manager }}/module/catalog_v2/file_upload"><span class="far fa-images"></span>Загрузка
                                изображений</a>
                        </li>
                        <li {{ menu_choose('cats') }}>
                            <a href="{{ $manager }}/cats">
                                <span class="glyphicon glyphicon-list"></span>
                                <span class="sidebar-title">Категории</span>
                            </a>
                        </li>
                        <li {{ menu_choose_module('catalog_v2', 'groups') }}>
                            <a href="{{ $manager }}/module/catalog_v2/groups"><span class="far fa-images"></span>Группы
                                товаров</a>
                        </li>
                        <li {{ menu_choose_module('price', 'catalog_price_rival') }}>
                            <a href="{{ $manager }}/module/price/catalog_price_rival"><span
                                        class="far fa-images"></span>Настройка цены</a>
                        </li>
                        <li {{ menu_choose_module('price', 'cpdo') }}>
                            <a href="{{ $manager }}/module/price/cpdo"><span class="far fa-images"></span>Привязка
                                скидок клиентам</a>
                        </li>
                        <li {{ menu_choose_module('price', 'cpdo:groups') }}>
                            <a href="{{ $manager }}/module/price/cpdo/groups"><span class="far fa-images"></span>Привязка
                                скидок клиентам по группе</a>
                        </li>
                        <li {{ menu_choose_module('price', 'catalog_price_rival:discount') }}>
                            <a href="{{ $manager }}/module/price/catalog_price_rival/discount"><span
                                        class="far fa-images"></span>Привязка скидок конкурентов</a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="javascript:;"
                       class="accordion-toggle {{ menu_open('reestr,command,user,groups,nomenclature,bank,inventory,doc,access') }}">
                        <span class="fa fa-building"></span>
                        <span class="sidebar-title">Администрирование</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {{ menu_choose('reestr') }}>
                            <a href="{{ $manager }}/module/reestr"><span class="fas fa-books"></span>Реестр
                                договоров</a>
                        </li>
                        <li {{ menu_choose('command') }}>
                            <a href="{{ $manager }}/command"><span class="fas fa-book"></span>Реестр
                                приказов</a>
                        </li>
                        <li {{ menu_choose('bank') }}>
                            <a href="{{ $manager }}/bank"><span class="fas fa-rub"></span>Банковская
                                выписка</a>
                        </li>
                        <li {{ menu_choose('inventory') }}>
                            <a href="{{ $manager }}/module/inventory"><span class="fas fa-qrcode"></span>Инвентаризация</a>
                        </li>
                        <li {{ menu_choose('user') }}>
                            <a href="{{ $manager }}/user"><span class="fas fa-user-tie"></span>Менеджер
                                пользователей</a>
                        </li>
                        <li {{ menu_choose('doc') }}>
                            <a href="{{ $manager }}/module/doc"><span class="fas fa-mail-bulk"></span>АТРИ-ДОК</a>
                        </li>
                        <li {{ menu_choose('groups') }}>
                            <a href="{{ $manager }}/groups"><span class="fas fa-layer-group"></span>Группы</a>
                        </li>
                        <li {{ menu_choose('access') }}>
                            <a href="{{ $manager }}/module/access"><span class="fas fa-user-shield"></span>Права доступа</a>
                        </li>
                        <li {{ menu_choose('org') }}>
                            <a href="{{ $manager }}/module/org"><span class="fas fa-building"></span>Изменение
                                организаций</a>
                        </li>
                        <li {{ menu_choose('org') }}>
                            <a href="{{ $manager }}/module/stocks"><span class="fas fa-warehouse"></span>Склады
                                организаций</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;" class="accordion-toggle {{ menu_open('report') }}">
                        <span class="fa fa-chart-pie"></span>
                        <span class="sidebar-title">Отчёты</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {{ menu_choose('report:storage') }}>
                            <a href="{{ $manager }}/module/report/storage"><span class="fas fa-file-contract"></span>Отчёт
                                по движению товара</a>
                        </li>
                        <li {{ menu_choose('report:storage_no_trans') }}>
                            <a href="{{ $manager }}/module/report/storage_no_trans"><span
                                        class="fas fa-file-contract"></span>Отчёт по фактическим остаткам</a>
                        </li>
                        <li {{ menu_choose('report:realization') }}>
                            <a href="{{ $manager }}/module/report/realization"><span
                                        class="fas fa-file-contract"></span>Отчёт по реализациям</a>
                        </li>
                        <li {{ menu_choose('report:realization_catalog') }}>
                            <a href="{{ $manager }}/module/report/realization_catalog"><span
                                        class="fas fa-file-contract"></span>Отчёт по реализации товаров</a>
                        </li>
                        <li {{ menu_choose('report:realization_catalog') }}>
                            <a href="{{ $manager }}/module/report/realization_all_catalog"><span
                                        class="fas fa-file-contract"></span>Отчёт по реализации товаров(подробный)</a>
                        </li>
                        <li {{ menu_choose('report:shipment_minus') }}>
                            <a href="{{ $manager }}/module/report/shipment_minus"><span
                                        class="fas fa-file-contract"></span>Отчёт по отгрузке товаров в минус</a>
                        </li>
                        <li {{ menu_choose('report:storage_sum') }}>
                            <a href="{{ $manager }}/module/report/storage_sum"><span
                                        class="fas fa-file-contract"></span>Отчёт по сумме на складах</a>
                        </li>
                        <li {{ menu_choose('report:storage_sum') }}>
                            <a href="{{ $manager }}/module/report/average_delivery_time"><span
                                        class="fas fa-file-contract"></span>Отчёт по средней времени доставки</a>
                        </li>
                    </ul>
                </li>
                @if (check_access('10'))
                    <li>
                        <a href="javascript:;"
                           class="accordion-toggle {{ menu_open('news,fm,xfields,comments,moderation,voting,board,content,modules,blocks,templates,guestbook,partner,project,certificates,pto,reviews') }}">
                            <span class="glyphicon glyphicon-globe"></span>
                            <span class="sidebar-title">Управление сайтом</span>
                            <span class="caret"></span>
                        </a>
                        <ul class="nav sub-nav">
                            <li {{ menu_choose('news') }}>
                                <a href="{{ $manager }}/module/news">
                                    <span class="far fa-newspaper"></span>Менеджер новостей
                                </a>
                            </li>
                            <li {{ menu_choose('xfields') }}>
                                <a href="{{ $manager }}/xfields">
                                    <span class="glyphicon glyphicon-star"></span>Дополнительные поля
                                </a>
                            </li>

                            <li>
                                <a href="javascript:;"
                                   class="accordion-toggle {{ menu_open('comments,moderation,voting,guestbook') }}">
                                    <span class="glyphicon glyphicon-user"></span>
                                    <span class="sidebar-title">Пользователи</span>
                                    <span class="caret"></span>
                                </a>
                                <ul class="nav sub-nav">
                                    <li {{ menu_choose('comments') }}>
                                        <a href="{{ $manager }}/comments"><span
                                                    class="glyphicon glyphicon-comment"></span>Комментарии</a>
                                    </li>
                                    <li {{ menu_choose('moderation') }}>
                                        <a href="{{ $manager }}/moderation"><span
                                                    class="glyphicon glyphicon-edit"></span>Модерация</a>
                                    </li>
                                    <li {{ menu_choose('voting') }}>
                                        <a href="{{ $manager }}/voting"><span
                                                    class="glyphicon glyphicon-plus-sign"></span>Голосования</a>
                                    </li>

                                    <li {{ menu_choose('guestbook') }}>
                                        <a href="{{ $manager }}/module/guestbook"><span
                                                    class="far fa-comment-alt-dots"></span>Гостевая книга</a>
                                    </li>

                                </ul>
                            </li>
                            <li>
                                <a href="javascript:;"
                                   class="accordion-toggle {{ menu_open('board,content,partner,project,certificates,pto,reviews') }}">
                                    <span class="glyphicon glyphicon-th-large"></span>
                                    <span class="sidebar-title">Компоненты</span>
                                    <span class="caret"></span>
                                </a>
                                <ul class="nav sub-nav">

                                    <li {{ menu_choose('board') }}>
                                        <a href="{{ $manager }}/module/board"><span class="fas fa-bullhorn"></span>Форум</a>
                                    </li>

                                    <li {{ menu_choose('content') }}>
                                        <a href="{{ $manager }}/module/content"><span class="far fa-sticky-note"></span>Статические
                                            страницы</a>
                                    </li>

                                    <li {{ menu_choose('partner') }}>
                                        <a href="{{ $manager }}/module/partner"><span class="far fa-handshake"></span>Менеджер
                                            партнёров</a>
                                    </li>

                                    <li {{ menu_choose('project') }}>
                                        <a href="{{ $manager }}/module/project"><span class="far fa-toolbox"></span>Менеджер
                                            объектов</a>
                                    </li>

                                    <li {{ menu_choose('certificates') }}>
                                        <a href="{{ $manager }}/module/certificates"><span
                                                    class="far fa-file-certificate"></span>Менеджер сертификатов</a>
                                    </li>

                                    <li {{ menu_choose('pto') }}>
                                        <a href="{{ $manager }}/module/pto"><span class="fas fa-layer-group"></span>Менеджер
                                            теплообменников</a>
                                    </li>

                                    <li {{ menu_choose('reviews') }}>
                                        <a href="{{ $manager }}/module/reviews"><span
                                                    class="far fa-comment-alt-dots"></span>Отзывы клиентов</a>
                                    </li>

                                </ul>
                            </li>

                            <li>
                                <a href="javascript:;"
                                   class="accordion-toggle {{ menu_open('modules,blocks,templates') }}">
                                    <span class="glyphicon glyphicon-send"></span>
                                    <span class="sidebar-title">Другое</span>
                                    <span class="caret"></span>
                                </a>
                                <ul class="nav sub-nav">
                                    <li {{ menu_choose('modules') }}>
                                        <a href="{{ $manager }}/modules"><span class="fa fa-cloud"></span>Модули</a>
                                    </li>
                                    <li {{ menu_choose('blocks') }}>
                                        <a href="{{ $manager }}/blocks"><span class="fa fa-code"></span>PHP
                                            Блоки</a>
                                    </li>
                                    <li {{ menu_choose('templates') }}>
                                        <a href="{{ $manager }}/templates"><span class="fa fa-pencil-square-o"></span>Шаблоны</a>
                                    </li>
                                </ul>
                            </li>

                        </ul>
                    </li>
                @endif
                <li>
                    <a href="javascript:;" class="accordion-toggle {{ menu_open('files') }}">
                        <span class="fas fa-folder-open"></span>
                        <span class="sidebar-title">Файловый менеджер</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {{ menu_choose_module('files', 'server') }}>
                            <a href="{{ $manager }}/module/files/server"><span class="fa fa-list"></span>Серверное
                                хранилище</a>
                        </li>
                        <li {{ menu_choose_module('files', 'project') }}>
                            <a href="{{ $manager }}/module/files/project"><span
                                        class="glyphicon glyphicon-ok-circle"></span>Проектирование</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;"
                       class="accordion-toggle {{ menu_open('sitemap,smiles,db,log,update,changelog,sync,daemon') }}">
                        <span class="glyphicon glyphicon-question-sign"></span>
                        <span class="sidebar-title">Другое</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {{ menu_choose('update') }}>
                            <a href="{{ $manager }}/module/object"><span
                                        class="glyphicon glyphicon-briefcase"></span><span
                                        class="sidebar-title">Объекты</span></a>
                        </li>
                        <li {{ menu_choose('config') }}>
                            <a href="{{ $manager }}/config">
                                <span class="glyphicon glyphicon-cog"></span>
                                <span class="sidebar-title">Настройки </span>
                            </a>
                        </li>
                        <li {{ menu_choose('changelog') }}>
                            <a href="{{ $manager }}/module/changelog"><span class="fa  fa-bullhorn"></span>История
                                изменений</a>
                        </li>
                        <li {{ menu_choose('sync') }}>
                            <a href="{{ $manager }}/module/sync"><span class="glyphicon  glyphicon-refresh"></span>Синхронизация
                                данных</a>
                        </li>
                        <li {{ menu_choose('log') }}>
                            <a href="{{ $manager }}/log"><span class="glyphicon glyphicon-info-sign"></span>Логи</a>
                        </li>
                        <li {{ menu_choose('update') }}>
                            <a href="{{ $manager }}/update"><span class="glyphicon glyphicon-download"></span>Обновления</a>
                        </li>
                        <li {{ menu_choose('daemon') }}>
                            <a href="{{ $manager }}/module/daemon"><span class="glyphicon  glyphicon-refresh"></span>Фоновые
                                процессы</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-label pt35"></li>
                <li {{ menu_choose('support') }}><a href="{{ $manager }}/module/support"><span
                                class="glyphicon glyphicon-bullhorn"></span><span class="sidebar-title">Поддержка</span></a>
                </li>
                <li {{ menu_choose('documentation') }}><a href="{{ $manager }}/module/documentation"><span
                                class="glyphicon glyphicon-paperclip"></span><span
                                class="sidebar-title">Документация</span></a>
                </li>
            </ul>
        </div>
    </aside>
    <section id="content_wrapper">
        <div id="topbar-dropmenu" class="alt">
            <div class="topbar-menu row">
                <div class="col-xs-4 col-sm-2">
                    <a href="{{ $manager }}/calendar" class="metro-tile bg-primary light">
                        <span class="glyphicon glyphicon-calendar text-muted"></span>
                        <span class="metro-title">Календарь</span>
                    </a>
                </div>
                <div class="col-xs-4 col-sm-2">
                    <a href="{{ $manager }}/module/sales" class="metro-tile bg-info light">
                        <span class="glyphicon glyphicon-calendar text-muted"></span>
                        <span class="metro-title">Продажи</span>
                    </a>
                </div>

                <div class="col-xs-4 col-sm-2">
                    <a href="{{ $manager }}/user" class="metro-tile bg-info light">
                        <span class="glyphicon glyphicon-user text-muted"></span>
                        <span class="metro-title">Пользователи</span>
                    </a>
                </div>

                <div class="col-xs-4 col-sm-2">
                    <a href="{{ $manager }}/module/content" class="metro-tile bg-success light">
                        <span class="glyphicon glyphicon-edit text-muted"></span>
                        <span class="metro-title">Статические страницы</span>
                    </a>
                </div>

                <div class="col-xs-4 col-sm-2">
                    <a href="{{ $manager }}/module/board" class="metro-tile bg-system light">
                        <span class="glyphicon glyphicon-comment text-muted"></span>
                        <span class="metro-title">Форум</span>
                    </a>
                </div>

                <div class="col-xs-4 col-sm-2">
                    <a href="{{ $manager }}/module/gallery" class="metro-tile bg-alert light">
                        <span class="glyphicon glyphicon-picture text-muted"></span>
                        <span class="metro-title">Галерия</span>
                    </a>
                </div>

                <div class="col-xs-4 col-sm-2">
                    <a href="{{ $manager }}/config" class="metro-tile bg-warning light">
                        <span class="fa fa-gears text-muted"></span>
                        <span class="metro-title">Настройки</span>
                    </a>
                </div>
            </div>
        </div>
        @yield('body')

    </section>
</div>
<style>
    .btn-toolbar > .btn-group.note-fontname {
        display: none;
    }
</style>

<script src="{{ $manager_theme }}/assets/js/demo.js"></script>
<script src="{{ $manager_theme }}/assets/js/main.js"></script>
<script src="{{ $manager_theme }}/assets/js/widgets.js"></script>

<script src="{{ $manager_theme }}/assets/js/bootstrap-switch.min.js"></script>
<script src="{{ $manager_theme }}/assets/js/jquery.qrcode.min.js"></script>
<script src="{{ $manager_theme }}/assets/js/jquery.smartWizard.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css"/>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="{{ $manager_theme }}/assets/css/offline-language-english.css">
<link rel="stylesheet" type="text/css" href="{{ $manager_theme }}/assets/css/offline-theme-dark.css">

{{-- {% for script in chat_data.script %}
    {{script|raw}}
{% endfor %} --}}

{{-- {% if app.request.query.get("chat") %}
    <script>
        $(function(){
            openChat();
            openChatId({{app.request.query.get("chat")}});
        });
    </script>
{% endif %} --}}

{{ $foot }}
{{-- {% block scripts %}{% endblock %} --}}

<script type="text/javascript">
    (function ($) {
        if (!$.curCSS) {
            $.curCSS = $.css;
        }
    })(jQuery);
    jQuery(document).ready(function () {
        "use strict";
        // Init Theme Core
        Core.init();
        // Init Demo JS
        Demo.init();
        $('input[id="switch"]').bootstrapSwitch('state', true, true);
        {{ $js_code }}
    });
</script>
<script>
    var run = function () {
        if (Offline.state === 'up')
            Offline.check();
    }
    setInterval(run, 5000);
    $(function () {

        var $offline = $('.overlay-offline');

        Offline.on('confirmed-down', function () {
            $offline.fadeIn();
        });

        Offline.on('confirmed-up', function () {
            $offline.fadeOut();
        });

    });
</script>
<script src="https://www.gstatic.com/firebasejs/3.6.8/firebase.js"></script>
<div class="offline"></div>
@livewireScripts
@stack('scripts')
</body>

</html>
