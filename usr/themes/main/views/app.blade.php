<!DOCTYPE html>
<html class="no-js" lang="{{ app()->getLocale() }}" dir="ltr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Language" content="{{ app()->getLocale() }}">

    {{--<meta property="og:site_name" content="@lang('app.site_title')">--}}
    {{--<meta property="og:title" content="@lang('app.site_title')">--}}
    {{--<meta property="og:description" content="@lang('app.site_desc')">--}}
    {{--<meta property="og:type" content="website">--}}
    {{--<meta property="og:locale" content="{{ app()->getLocale() }}">--}}
    {{--<meta property="og:url" content="{{ url()->current() }}">--}}


    {{--<title>@yield('title', trans('app.site_title'))</title>--}}
    <title>Официальный сайт - ООО «АТРИ-ЭНЕРГО»</title>


    <link rel="preload" as="style" onload="this.rel='stylesheet'" type="text/css"
          href="{{ asset('assets/fonts/fontawesome/5.12.0/css/all.min.css') }}">

    <noscript>
        <link type="text/css" href="{{ asset('assets/fonts/fontawesome/5.12.0/css/all.min.css') }}"
              rel="stylesheet">
    </noscript>

    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/vendor/animate-3.7.2/animate.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/vendor/bootstrap-4.3.1/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">

    <script type="text/javascript"
            src="{{ asset('assets/vendor/jquery-3.6.0/jquery-3.6.0.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/flaticon/flaticon.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/industry/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/shortcodes.css') }}">

    <meta name="description" content="@yield('meta_description', __('app.site_desc'))">
    <link rel="canonical" href="{{ url()->current() }}">

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    <script src="{{ asset('assets/js/priceList.js') }}" charset="utf-8"></script>

</head>
<body>

<div class="body_wrapper">

    <header>

        <div class="header-top">
            <div class="container">
                <div class="row sidebar-top">

                    {{-- Социальный блок --}}
                    <div class="socials-header">
                        <ul>
                            <li tooltip="Вконтакте" flow="down">
                                <a href="https://vk.com/atrienergo" target="_blank">
                                    <span class="fab fa-vk"></span>
                                </a>
                            </li>
                            <li tooltip="Telegram" flow="down">
                                <a href="#" target="_blank">
                                    <span class="fab fa-telegram"></span>
                                </a>
                            </li>
                            <li tooltip="Whatsapp" flow="down">
                                <a href="#" target="_blank">
                                    <span class="fab fa-whatsapp"></span>
                                </a>
                            </li>
                            <li tooltip="YouTube" flow="down">
                                <a href="#" target="_blank">
                                    <span class="fab fa-youtube"></span>
                                </a>
                            </li>
                            <li tooltip="E-mail" flow="down">
                                <a href="mailto:info@atri-energo.ru" target="_blank">
                                    <span class="fas fa-envelope"></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    {{-- Социальный блок --}}

                    <div class="sidebar-top-right">

                        {{-- Телефон --}}
                        <div class="block-text">
                            <i class="fa fa-phone"></i>
                            @lang('app.phone_short'): <span class="number-phone">
                            <a href="tel:@lang('app.phone_num')">
                                @lang('app.phone_num_text')
                            </a>
                        </span>
                        </div>
                        {{-- Телефон --}}

                        <div id="sidebar-top" class="pull-right">

                            {{-- Смена языка --}}
                            <div class="widget_icl_lang_sel_widget">
                                <div class="widget-inner">
                                    <div class="textwidget">
                                        <div class="lang_sel">
                                            <ul class="nav">
                                                <li class="pl_style">
                                                    <a class="lang_sel_sel icl-en t_lang" href="javascript:;">
                                                        <img class="iclflag" alt="ru"
                                                             src="{{ asset('assets/images/lang/ru.gif') }}"
                                                             width="18"
                                                             height="12"/>
                                                        @lang('app.lang.ru')
                                                    </a>
                                                    <ul class="switch_lang widget_nav_menu_user">
                                                        <li class="icl-en">
                                                            <a href="{{ url('lang/ru') }}">
                                                                <img class="iclflag" alt="ru"
                                                                     src="{{ asset('assets/images/lang/ru.gif') }}"
                                                                     width="18"
                                                                     height="12"/>
                                                                @lang('app.lang.ru')
                                                            </a>
                                                        </li>
                                                        <li class="icl-ar">
                                                            <a href="{{ url('lang/en') }}">
                                                                <img class="iclflag" alt="en"
                                                                     src="{{ asset('assets/images/lang/en.png') }}"
                                                                     width="18"
                                                                     height="12"/>
                                                                @lang('app.lang.en')
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Смена языка --}}

                            {{-- Избранное --}}
                            <div class="widget nav_menu-2 widget_nav_menu pull-right">
                                <div class="widget-inner">
                                    <ul id="menu-my-wishlist" class="menu">
                                        <li class="header-top-menu has-icon menu-my-wishlist">
                                            <a class="item-link" href="{{ url('favorite') }}">
                                                <div class="izbrannoe">
                                                    <span class="fa fa fa-heart"></span>
                                                    <span class="menu-title">
                                                    @lang('app.favorite')
                                                </span>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            {{-- Избранное --}}

                            {{-- Сравнение --}}
                            <div class="widget nav_menu-2 widget_nav_menu pull-right">
                                <div class="widget-inner">
                                    <ul id="menu-my-wishlist" class="menu">
                                        <li class="header-top-menu has-icon menu-my-wishlist compare-link">
                                            <a class="item-link" href="{{ url('catalog_v2/compare') }}">
                                                <div class="izbrannoe">
                                                    <span class="fa fa-balance-scale"></span>
                                                    <span class="menu-title">
                                                    @lang('app.compare')
                                                </span>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            {{-- Сравнение --}}

                            {{-- Личный кабинет --}}
                            <div class="top-header-sidebar-menu pull-right">
                                <div class="my-account">
                                <span class="user-profile">
                                    <i class="fa fa-user"></i>
                                    <span>
                                        @lang('app.profile')
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                </span>
                                </div>
                                <div id="sidebar-login-top" class="sidebar-login-top">

                                    @auth()
                                        <div class="widget_nav_menu_user">
                                            <div class="widget-inner">
                                                <ul id="menu-my-account" class="menu">

                                                    {{-- Корпоративный портал --}}
                                                    @can('portal access')

                                                        <li class="menu-wishlist">
                                                            <a class="item-link" href="{{ url('admin') }}">
                                                                <i class="fas fa-users"></i>
                                                                <span class="menu-title">
                                                                    @lang('app.corp')
                                                                </span>
                                                            </a>
                                                        </li>

                                                    @endcan
                                                    {{-- Корпоративный портал --}}

                                                    <li class="menu-wishlist">
                                                        <a class="item-link" href="{{ url('profile') }}">
                                                            <i class="fas fa-user"></i>
                                                            <span class="menu-title">
                                                                @lang('app.my_profile')
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <li class="menu-my-account">
                                                        <a class="item-link" href="{{ url('profile/order') }}">
                                                            <i class="fas fa-shopping-basket"></i>
                                                            <span class="menu-title">
                                                                @lang('app.my_order')
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <li class="menu-my-account">
                                                        <a class="item-link" href="{{ url('profile/request') }}">
                                                            <i class="fas fa-calculator"></i>
                                                            <span class="menu-title">
                                                                @lang('app.my_calc')
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <li class="menu-my-account">
                                                        <a class="item-link"
                                                           href="{{ url('profile/specification') }}"
                                                        >
                                                            <i class="fas fa-bars"></i>
                                                            <span class="menu-title">
                                                                @lang('app.my_spec')
                                                            </span>
                                                        </a>
                                                    </li>

                                                    <li class="menu-checkout">
                                                        <a class="item-link" href="{{ url('profile/logout') }}">
                                                            <i class="fas fa-sign-out"></i>
                                                            <span class="menu-title">
                                                                @lang('app.logout')
                                                            </span>
                                                        </a>
                                                    </li>

                                                </ul>
                                            </div>
                                        </div>
                                    @endauth

                                    @guest()
                                        <div id="flytheme_top-2" class="widget_nav_menu_user widget_nav_menu_user_no">
                                            <div class="widget-inner">
                                                <div class="top-login pull-right">
                                                    <ul>
                                                        <li>
                                                            <a href="{{ url('profile/login') }}">
                                                                <i class="fa fa-sign-out"></i>
                                                                <span>
                                                                @lang('app.login')
                                                            </span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ url('profile/register') }}">
                                                                <i class="fa fa-id-card"></i>
                                                                <span>
                                                                @lang('app.register')
                                                            </span>
                                                            </a>
                                                        </li>

                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endguest

                                </div>
                            </div>
                            {{-- Личный кабинет --}}

                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>

        <div class="container top container-block">
            <div class="row">

                <div class="col-md-12 header-app">

                    {{-- Лого --}}
                    <div class="top-header header-ct-left">
                        <div class="flytheme-logo pull-left">
                            <a href="{{ url('/') }}">
                                <div class="logo">
                                    <div class="logo-icon">
                                        <picture>
                                            <source srcset="{{ asset('assets/images/logo1.webp') }}"
                                                    type="image/webp">
                                            <img src="{{ asset('assets/images/logo1.png') }}" alt="logo"/>
                                        </picture>
                                    </div>
                                    <div class="logo-text">
                                        <div class="logo-blick"></div>
                                        <picture class="logo-img2">
                                            <source srcset="{{ asset('assets/images/logo2.webp') }}"
                                                    type="image/webp">
                                            <img src="{{ asset('assets/images/logo2.png') }}" alt="logo"/>
                                        </picture>
                                        <picture class="logo-img3">
                                            <source srcset="{{ asset('assets/images/logo3.webp') }}"
                                                    type="image/webp">
                                            <img src="{{ asset('assets/images/logo3.png') }}" alt="logo">
                                        </picture>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    {{-- Лого --}}

                    {{-- Поиск --}}
                    <div id="sidebar-top-header" class="header-ct-center">
                        <div class="widget flytheme_top-3 flytheme_top non-margin search-form">
                            <div class="widget-inner">
                                <div class="top-form top-search pull-left">
                                    <div class="topsearch-entry">
                                        <form id="searchform_special" method="POST"
                                              action="{{ url('catalog/search') }}">
                                            <div>
                                                <input type="text" class="who" value="" name="referal"
                                                       id="autocomplete-ajax"
                                                       placeholder="@lang('app.search_placeholder')"
                                                       autocomplete="off"/>
                                                <button onclick="return false" type="submit"
                                                        class="button-search-pro form-button">
                                                    <i class="fa fa-search"></i>
                                                    @lang('app.search')
                                                </button>
                                                <input type="hidden" name="search_posttype" value="product"/>
                                                <div class="overlay_search"
                                                     style="display: none; position: fixed; left: 0; top: 0; right: 0; bottom: 0;"></div>
                                                <ul class="search_result"></ul>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Поиск --}}

                    {{-- Корзина --}}
                    <div class="mycart header-ct-right">
                        <div id="sidebar-top-menu" class="sidebar-top-menu pull-right">
                            <div class="top-form top-form-minicart flytheme-minicart pull-right"
                                 onclick="cartBasket()"
                            >
                                <div class="top-minicart-icon pull-right noselect">
                                <span class="minicart-number">
                                    <i class="fas fa-receipt"></i>
                                    <span id="basketwidjet">0</span>
                                </span>
                                    <span class="shopping-text">
                                    @lang('app.shopping_cart')
                                </span>
                                    <span class="woocommerce-Price-amount amount" id="basketwidjetSum">0.00</span>
                                </div>
                                <div class="wrapp-minicart">
                                    <div class="minicart-padding">
                                        <ul class="minicart-content"></ul>
                                        <div class="cart-checkout">
                                            <div class="price-total">
                                                <span class="label-price-total">Итого:</span>
                                                <div class="price-total-w">
                                                <span class="price">
                                                    <span class="woocommerce-Price-amount amount" id="basketwidjetSum2">
                                                        0.00
                                                    </span>
                                                </span>
                                                </div>
                                            </div>
                                            <div class="cart-links">
                                                <div class="cart-link">
                                                    <a href="{{ url('cart') }}" title="В спецификацию">
                                                        В спецификацию
                                                    </a>
                                                </div>
                                                <div class="checkout-link">
                                                    <a href="{{ url('/') }}" title="Оформить">
                                                        Оформить
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Корзина --}}

                </div>

            </div>
        </div>

        <div class="menu-list-nav"></div>

        <div id="header-menu">
            <div class="header-menu-wrap">
                <div class="header-menu-right">

                    {{-- Сравнение --}}
                    <a href="{{ url('catalog_v2/compare') }}">
                        <i class="fas fa-balance-scale compare-act" aria-hidden="true">
                            <span class="compare-number">0</span>
                        </i>
                    </a>
                    {{-- Сравнение --}}

                    {{-- Избранное --}}
                    <a href="{{ url('favorite') }}">
                        <i class="far fa-heart" aria-hidden="true">
                            <span>0</span>
                        </i>
                    </a>
                    {{-- Избранное --}}

                    {{-- Поиск --}}
                    <i class="fas fa-search d-none" onclick="searchFormClick()" aria-hidden="true"></i>
                    {{-- Поиск --}}

                    {{-- Заявки --}}
                    <i onclick="cartBasket()" class="fas fa-receipt" aria-hidden="true">
                        <span>0</span>
                    </i>
                    {{-- Заявки --}}

                    <div class="nav-mob">
                        <div id="nav-icon">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="clear"></div>

    </header>

    {{-- Главное меню --}}
    @include('m_menu')
    {{-- Главное меню --}}

    {{-- Контент --}}
    @yield('content')
    {{-- Контент --}}

    {{-- go to top --}}
    <a id="flytheme-totop" href="#"><i class="fa fa-angle-double-up" aria-hidden="true"></i></a>
    {{-- go to top --}}

    {{--
    <div id="login_form" class="d-none" tabindex="-1" aria-hidden="true">
        <div class="box-modal" id="exampleModal">
            <div class="box-modal_close arcticmodal-close">закрыть</div>
            <div class="block-popup-login">
                <div class="tt_popup_login">
                    <h2>Войти или зарегистрироваться</h2>
                </div>
                <form action="{{ url('profile/login') }}" method="post" class="login">
                    <div class="block-content">
                        <div class="col-reg registered-account">
                            <div class="email-input">
                                <input type="text" class="form-control input-text username" name="nick"
                                       id="fastEmail" placeholder="E-mail или логин"/>
                            </div>
                            <div class="pass-input">
                                <input class="form-control input-text password" type="password" placeholder="Пароль"
                                       name="password" id="fastPassword"/>
                            </div>
                            <div class="ft-link-p">
                                <a href="{{ url('profile/forgot_pass') }}" title="Забыли пароль?">Забыли пароль?</a>
                            </div>
                            <div class="actions">
                                <div class="submit-login">
                                    <input type="submit" class="button btn-submit-login" name="login"
                                           value="Войти" />
                                </div>
                            </div>
                        </div>
                        <div class="col-reg login-customer">
                            <h2>Вы у нас впервые?</h2>
                            <p class="note-reg">Зарегистрируйтесь это бесплатно и легко!</p>
                            <ul class="list-log">
                                <li>Индивидуальные скидки</li>
                                <li>Доступ к инструментам</li>
                                <li>Личный кабинет</li>
                            </ul>
                            <a href="{{ url('profile/register') }}" title="Создать аккаунт" class="btn-reg-popup">
                                Создать аккаунт
                            </a>
                        </div>
                    </div>
                </form>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    --}}

    {{-- Модальное окно - Мы на карте --}}
    <div id="map_form" class="d-none" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="box-modal" id="mapModal">
            <div class="box-modal_close arcticmodal-close">
                <i class="fas fa-times h4"></i>
            </div>
            <div class="tt_popup_map">
                <h2>Мы на карте</h2>
            </div>
            <div class="mapModalClose">
                <script type="text/javascript" charset="utf-8" defer
                        src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A2282d7c33b0537643e7c6e269b52c13d64d56971b3285efe36363dc704d113bd&amp;width=100%25&amp;height=100%25&amp;lang=ru&amp;scroll=false"></script>
            </div>
        </div>
    </div>
    {{-- Модальное окно - Мы на карте --}}

    {{-- Футер --}}
    <footer class="footer theme-clearfix footer-style1
{{--            @if($footer_class) {{ $footer_class }} @else footer-st @endif"--}}
        ">
        <div class="container container-block theme-clearfix">
            <div class="footer-top">
                <div class="row">
                    <div class="col-lg-12 row">

                        <div class="col widget nav_menu-4 widget_nav_menu">
                            <div class="widget-inner">
                                <h3><span>Полезное</span></h3>
                                <ul id="menu-my-account-footer" class="menu">
                                    <li class="menu-information">
                                        <a class="item-link" href="{{ url('page/about') }}">
                                            <span class="menu-title">О компании</span>
                                        </a>
                                    </li>
                                    <li class="menu-oders-history">
                                        <a class="item-link" href="{{ url('news') }}">
                                                <span class="menu-title">
                                                    Новости
                                                </span>
                                        </a>
                                    </li>
                                    <li class="menu-my-wishlist">
                                        <a class="item-link" href="{{ url('partner') }}">
                                                <span class="menu-title">
                                                    Наши партнеры
                                                </span>
                                        </a>
                                    </li>
                                    <li class="menu-privacy-policy">
                                        <a class="item-link" href="{{ url('object') }}">
                                                <span class="menu-title">
                                                    Наши объекты
                                                </span>
                                        </a>
                                    </li>
                                    <li class="menu-privacy-policy">
                                        <a class="item-link" href="{{ router('job.index') }}">
                                                <span class="menu-title">
                                                    Вакансии
                                                </span>
                                        </a>
                                    </li>
                                    <li class="menu-site-map">
                                        <a class="item-link" href="{{ url('feedback') }}">
                                                <span class="menu-title">
                                                    Контакты
                                                </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col widget nav_menu-5 widget_nav_menu">
                            <div class="widget-inner">
                                <h3>
                                        <span>
                                            Информация
                                        </span>
                                </h3>
                                <ul id="menu-infomation" class="menu">
                                    <li class="menu-wishlist">
                                        <a class="item-link" href="{{ url('content/documents.html') }}">
                                                <span class="menu-title">
                                                    Документы
                                                </span>
                                        </a>
                                    </li>
                                    <li class="menu-custom-services">
                                        <a class="item-link" href="{{ url('certificates') }}">
                                                <span class="menu-title">
                                                    Сертификаты
                                                </span>
                                        </a>
                                    </li>
                                    <li class="menu-contact-us">
                                        <a class="item-link" href="{{ url('content/service_price.html') }}">
                                            <span class="menu-title">Прайс-листы на сервис</span>
                                        </a>
                                    </li>
                                    <li class="menu-privacy-policy">
                                        <a class="item-link" href="{{ url('content/privacy_policy.html') }}">
                                                <span class="menu-title">
                                                    Политика конфиденциальности
                                                </span>
                                        </a>
                                    </li>
                                    <li class="menu-information">
                                        <a class="item-link" href="{{ url('sitemap') }}">
                                                <span class="menu-title">
                                                    Карта сайта
                                                </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col widget nav_menu-6 widget_nav_menu">
                            <div class="widget-inner">
                                <h3>
                                        <span>
                                            Наши услуги
                                        </span>
                                </h3>
                                <ul id="menu-our-services" class="menu">
                                    <li class="menu-privacy-policy">
                                        <a class="item-link" href="{{ url('catalog_v2') }}">
                                                <span class="menu-title">
                                                    Каталог продукции
                                                </span>
                                        </a>
                                    </li>
                                    <li class="menu-wishlist">
                                        <a class="item-link" href="{{ url('content/project.html') }}">
                                                <span class="menu-title">
                                                    Проектирование
                                                </span>
                                        </a>
                                    </li>
                                    <li class="menu-custom-services">
                                        <a class="item-link" href="{{ url('page/mounting') }}">
                                                <span class="menu-title">
                                                    Монтаж
                                                </span>
                                        </a>
                                    </li>
                                    <li class="menu-information">
                                        <a class="item-link" href="{{ url('service') }}">
                                                <span class="menu-title">
                                                    Сервис центр
                                                </span>
                                        </a>
                                    </li>
                                    <li class="menu-contact-us">
                                        <a class="item-link" href="{{ url('page/servicing') }}">
                                                <span class="menu-title">
                                                    Обслуживание
                                                </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col">
                            <div id="text-4" class="widget-1 widget-first widget text-4 widget_text">
                                <div class="widget-inner">
                                    <h3>Наши контакты</h3>
                                    <div class="textwidget">
                                        <div class="contact-footer">
                                            <ul class="contact">
                                                <li>
                                                    <span class="fa fa-map-marker"></span>
                                                    <span class="label-footer">Адрес:</span>
                                                    <a href="#" onclick="mapModal(); return false;">
                                                        @lang('app.address')
                                                    </a>
                                                </li>
                                                <li>
                                                    <span class="fa fa-at"></span>
                                                    <span class="label-footer">E-mail:</span>
                                                    <a href="mailto:@lang('app.email')">
                                                        @lang('app.email')
                                                    </a>
                                                </li>
                                                <li class="contact-phone">
                                                    <span class="fa fa-phone"></span>
                                                    <span class="label-footer">Телефон:</span>
                                                    <a href="tel:@lang('app.phone_num')">
                                                        @lang('app.phone_num_text')
                                                    </a>
                                                </li>
                                            </ul>

                                            {{-- Социальные сети --}}
                                            <ul class="socials-footer">
                                                <li>
                                                    <a href="https://vk.com/atrienergo" title="Наша группа Вконтакте">
                                                        <span class="fab fa-vk"></span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" title="Telegram">
                                                        <span class="fab fa-telegram"></span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" title="Whatsapp">
                                                        <span class="fab fa-whatsapp"></span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" title="YouTube">
                                                        <span class="fab fa-youtube"></span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="mailto:info@atri-energo.ru" title="E-mail">
                                                        <span class="fas fa-envelope"></span>
                                                    </a>
                                                </li>
                                            </ul>
                                            {{-- Социальные сети --}}

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="copyright theme-clearfix">
            <div class="container clearfix">
                <div class="copyright-text pull-left">
                    <p><br>Копирование материалов сайта без активной ссылки на оригинал запрещено! <br>Copyright ©
                        2012 – {{ date('Y') }} - Все права защищены!</p>
                </div>
                <div class="sidebar-copyright pull-right">
                    <div id="text-5" class="widget-1 widget-first widget text-5 widget_text">
                        <div class="widget-inner">
                            <br>
                            <a href="https://izh.one" target="_blank" title="Автоматизация бизнеса в Ижевске"
                               style="text-decoration: none; color: #d9d9d9;">
                                IZH.ONE
                            </a> - автоматизация бизнеса в Ижевске!<br>Powered by
                            <a target="_blank" href="https://jmy.su" title="JMY CMS - система управление сайтом"
                               style="text-decoration: none; color: #d9d9d9;">
                                JMY CMS
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    {{-- Футер --}}

</div>

{{-- Спецификация - Корзина --}}
<div id="footerLine" class="color_theme">

    <div class="footerLineKb"></div>

    <div class="footerLineWrap">
        <div class="footerLineWrapB1">
            <h4>Спецификация</h4>
            <div class="footerLineWrapB1Span">
                <i class="fa fa-check mr-1" aria-hidden="true"></i>
                Готовые к заказу [ <span class="mark" id="basketwidjet2">0</span> ]
            </div>
            <button class="btn btn-default" type="button"
                    href="javascript:void(0)" onclick="cart.clearBasket();"
                    name="ms2_action"
            >
                <i class="fa fa-trash mr-1" aria-hidden="true"></i>
                Очистить спецификацию
            </button>
        </div>
    </div>

    <div class="footerLineWrap">
        <div class="lineWrapTableH">
            <div></div>
            <div>Наименование</div>
            <div class="text-center">Колличество</div>
            <div class="text-center">Ед. изм.</div>
            <div class="text-center">Цена</div>
            <div class="text-center">Сумма</div>
            <div></div>
        </div>

        <div id="btable" class="btable">
            {{-- тут таблица --}}
        </div>

    </div>

    <div class="footerLineWrap">
        <div class="fds">
            <div class="myTable">
                <div class="table table-hover" id="tCart">

                    <div class="footer-cart">
                        <div class="total_cost text-center">
                            <span class="cart_itog">Итого:</span>
                            <span class="ms2_total_cost" id="bsum">0 ₽</span>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <div class="footerLineWrap footerLineWrapBt">
        <div class="cmm-box-right">

            <div class="cart-bot-home">
                <button type="button" onclick="cartBasket()" id="shopTj" class="btn btn-default">
                    Продолжить покупки
                </button>
                <p>Вернуться к выбору товара</p>
            </div>

            <div></div>

            <div class="cart-bot-home-bot">
                <button type="button" id="getSpecification" class="btn btn-default">
                    Получить спецификацию
                </button>
                <p>Вы получите спецификацию в формате <a title="Что это за формат?"
                                                         href="https://www.reviversoft.com/ru/file-extensions/docx"
                                                         target="_blank" class="text-primary">.docx</a></p>
            </div>

            <div class="cart-bot-home-bot">
                <button type="button" id="saveSpecification" class="btn btn-default">
                    Сохранить спецификацию
                </button>
                <p>Ваша спецификация сохранится в личном кабинете</p>
            </div>

            <div class="cart-bot-home-bot-bg">
                <button type="button" id="checkoutBtn"
                        data-type="@auth{{ 'user' }}@endauth{{''}}@guest{{ 'guest' }}@endguest"
                        class="btn btn-default"
                >
                    Оформить заявку
                </button>
                <p>Наши специалисты свяжутся с Вами</p>
            </div>

        </div>
    </div>

</div>
{{-- Спецификация - Корзина --}}

<link rel="stylesheet" type="text/css"
      href="{{ asset('assets/vendor/select2-develop/dist/css/select2.min.css') }}"/>
<link rel="stylesheet" type="text/css"
      href="{{ asset('assets/vendor/pnotify/lib/PNotify.css') }}"/>
<link rel="stylesheet" type="text/css"
      href="{{ asset('assets/vendor/fancybox-3.5.7/dist/jquery.fancybox.min.css') }}"/>
<link rel="stylesheet" type="text/css"
      href="{{ asset('assets/vendor/x-editable-1.5.3/dist/bootstrap4-editable/css/bootstrap-editable.css') }}"/>
<link rel="stylesheet" type="text/css"
      href="{{ asset('assets/vendor/tempusdominus/build/css/tempusdominus-bootstrap-4.min.css') }}"/>
<link rel="stylesheet" type="text/css"
      href="{{ asset('assets/vendor/owlcarusel-2.3.4/dist/assets/owl.carousel.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('assets/css/tooltip.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/wicart.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/wicart.num.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/slick.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/slick-theme.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/jquery.arcticmodal-0.3.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/simple.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/hc-offcanvas-nav.css') }}">
<script type="text/javascript" src="{{ asset('assets/vendor/popper/popper.min.js') }}"></script>
<script type="text/javascript"
        src="{{ asset('assets/vendor/bootstrap-4.3.1/dist/js/bootstrap.min.js') }}"></script>
<script type="text/javascript"
        src="{{ asset('assets/vendor/select2-develop/dist/js/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/vendor/pnotify/lib/PNotify.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/vendor/pnotify/lib/PNotifyButtons.js') }}"></script>
<script type="text/javascript"
        src="{{ asset('assets/vendor/fancybox-3.5.7/dist/jquery.fancybox.min.js') }}"></script>
<script type="text/javascript"
        src="{{ asset('assets/vendor/x-editable-1.5.3/dist/bootstrap4-editable/js/bootstrap-editable.min.js') }}"></script>
<script type="text/javascript"
        src="{{ asset('assets/vendor/moment-2.24.0/min/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/vendor/moment-2.24.0/locale/ru.js') }}"></script>
<script type="text/javascript"
        src="{{ asset('assets/vendor/tempusdominus/build/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<script type="text/javascript"
        src="{{ asset('assets/vendor/sweetalert2-9.7.2/dist/sweetalert2.all.min.js') }}"></script>
<script type="text/javascript"
        src="{{ asset('assets/vendor/owlcarusel-2.3.4/dist/owl.carousel.min.js') }}"></script>
<script src="{{ asset('assets/js/hc-offcanvas-nav.js') }}"></script>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        loadMenu();
        let $toggle = $('.nav-mob');
        $('#main-nav').hcOffcanvasNav({

            maxWidth: '1120',
            customToggle: $toggle,
            navTitle: 'Меню',
            levelTitles: true,
            pushContent: false,
            insertClose: false,
            labelBack: 'Назад'
        });

    });
</script>
<script src="{{ asset('assets/js/slick.min.js') }}" type="text/javascript" charset="utf-8"></script>
<script src="{{ asset('assets/js/jquery.arcticmodal-0.3.min.js') }}" type="text/javascript"
        charset="utf-8"></script>
<script type="text/javascript" src="{{ asset('assets/js/wicart.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/wicart.num.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.cookie.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/chart.min.js') }}"></script>

{{-- Скрипт диапазона цен --}}
<script src="{{ asset('assets/js/jquery.ui.touch-punch.min.js') }}"></script>
{{-- Скрипт диапазона цен --}}

<script src="{{ asset('assets/js/scripts.js') }}" charset="utf-8"></script>
<script src="{{ asset('assets/js/catalogView.js') }}" charset="utf-8"></script>

<script src="{{ asset('assets/js/teplo.js') }}" charset="utf-8"></script>

<script src="{{ asset('assets/js/jquery.zoom.min.js') }}" charset="utf-8"></script>

{{--{# (FOOT) #}--}}

<!-- Yandex.Metrika counter -->
<script type="text/javascript" defer>
    (function (m, e, t, r, i, k, a) {
        m[i] = m[i] || function () {
            (m[i].a = m[i].a || []).push(arguments)
        };
        m[i].l = 1 * new Date();
        k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
    })(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
    ym(47376244, "init", {
        clickmap: true,
        trackLinks: true,
        accurateTrackBounce: true,
        webvisor: true,
        trackHash: true
    });
</script>

<noscript>
    <div><img src="https://mc.yandex.ru/watch/47376244" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript>
<!-- /Yandex.Metrika counter -->

<!-- Meta Pixel Code -->
{{--<script>--}}
{{--    !function (f, b, e, v, n, t, s) {--}}
{{--        if (f.fbq) return;--}}
{{--        n = f.fbq = function () {--}}
{{--            n.callMethod ?--}}
{{--                n.callMethod.apply(n, arguments(sad)n.queue.push(arguments)--}}
{{--        };--}}
{{--        if (!f._fbq) f._fbq = n;--}}
{{--        n.push = n;--}}
{{--        n.loaded = !0;--}}
{{--        n.version = '2.0';--}}
{{--        n.queue = [];--}}
{{--        t = b.createElement(e);--}}
{{--        t.async = !0;--}}
{{--        t.src = v;--}}
{{--        s = b.getElementsByTagName(e)[0];--}}
{{--        s.parentNode.insertBefore(t, s)--}}
{{--    }(window, document, 'script',--}}
{{--        'https://connect.facebook.net/en_US/fbevents.js');--}}
{{--    fbq('init', '670230744332724');--}}
{{--    fbq('track', 'PageView');--}}
{{--</script>--}}

{{--<noscript><img height="1" width="1" style="display:none"--}}
{{--               src="https://www.facebook.com/tr?id=670230744332724&ev=PageView&noscript=1"--}}
{{--    /></noscript>--}}
<!-- End Meta Pixel Code -->

</body>
</html>