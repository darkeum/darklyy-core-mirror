<div class="primary-menu-right primary-menu-home1 pull-left">
    <div id="main-menu" class="main-menu">

        <nav id="primary-menu" class="primary-menu">
            <div class="container container-block p-0">

                <div class="mid-header clearfix">
                    <div class="navbar-inner navbar-inverse">

                        {{--<div class="resmenu-container">--}}
                        {{--    <div id="ResMenuprimary_menu" class="collapse menu-responsive-wrapper">--}}
                        {{--        <ul id="menu-primary-menu" class="flytheme_resmenu"></ul>--}}
                        {{--    </div>--}}
                        {{--</div>--}}

                        <ul id="menu-primary-menu-1"
                            class="nav menu-slinky nav-pills nav-mega flytheme-mega flytheme-menures">

                            {{-- Главная --}}
                            <li class="home-fix menu-home flytheme-mega-menu flytheme-menu-icon {{ request()->is('/') ? 'active' : '' }}">
                                <a href="{{ url('/') }}" class="item-link">
                                    <span class="have-icon have-title">
                                        <span class="fa fa fa-home"></span>
                                        <span class="menu-title">
                                            @lang('menu.m_home')
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- Главная --}}

                            {{-- Каталог --}}
                            <li class="menu-shop flytheme-mega-menu menu-top-cat {{ request()->is('/catalog_v2') ? 'active' : '' }}">
                                <a href="/catalog_v2" class="item-link">
                                    <span class="have-title">
                                        <span class="menu-title">
                                            @lang('menu.m_catalog')
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- Каталог --}}

                            {{-- О компании --}}
                            <li class="menu-blog flytheme-mega-menu drop-about drop-about-company {{ request()->is(
                                            'page/about',
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
                                            @lang('menu.m_about')
                                        </span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu">

                                    <li class="menu-left-sidebar-grid {{ request()->is('news', 'news*') ? 'active' : '' }}">
                                        <a href="{{ url('news') }}">
                                            <span class="have-title">
                                                <i class="fad fa-newspaper"></i>
                                                <span class="menu-title">
                                                    @lang('menu.m_news')
                                                </span>
                                            </span>
                                        </a>
                                    </li>

                                    <li class="menu-right-sidebar-grid {{ request()->is('reviews', 'reviews/*') ? 'active' : '' }}">
                                        <a href="{{ url('reviews') }}">
                                            <span class="have-title">
                                                <i class="fad fa-comments"></i>
                                                <span class="menu-title">
                                                    @lang('menu.m_reviews')
                                                </span>
                                            </span>
                                        </a>
                                    </li>

                                    <li class="menu-full-width-grid menu-doc {{ request()->is('documents', 'documents/*') ? 'active' : '' }}">
                                        <a href="{{ url('content/documents.html') }}">
                                            <i class="fad fa-file-alt"></i>
                                            <span class="have-title">
                                                <span class="menu-title">
                                                    @lang('menu.m_documents')
                                                </span>
                                            </span>
                                        </a>
                                    </li>

                                    <li class="menu-left-sidebar-list {{ request()->is('certificates', 'certificates/*') ? 'active' : '' }}">
                                        <a href="{{ url('certificates') }}">
                                            <i class="fad fa-address-card"></i>
                                            <span class="have-title">
                                                <span class="menu-title">
                                                    @lang('menu.m_certs')
                                                </span>
                                            </span>
                                        </a>
                                    </li>

                                    <li class="menu-right-sidebar-list menu-object {{ request()->is('portfolio', 'portfolio/*') ? 'active' : '' }}">
                                        <a href="{{ url('portfolio') }}">
                                            <span class="have-title">
                                                <i class="fad fa-building"></i>
                                                <span class="menu-title">
                                                    @lang('menu.m_portfolio')
                                                </span>
                                            </span>
                                        </a>
                                    </li>

                                    <li class="menu-right-sidebar-list menu-partner {{ request()->is('partner', 'partner/*') ? 'active' : '' }}">
                                        <a href="{{ url('partner') }}">
                                            <span class="have-title">
                                                <i class="fad fa-handshake"></i>
                                                <span class="menu-title">
                                                    @lang('menu.m_partners')
                                                </span>
                                            </span>
                                        </a>
                                    </li>

                                </ul>
                            </li>
                            {{-- О компании --}}

                            {{-- Проект --}}
                            <li class="menu-blog flytheme-mega-menu drop-about {{ request()->is('project', 'project/*') ? 'active' : '' }}">
                                <a href="{{ url('project') }}" class="item-link">
                                    <span class="have-title">
                                        <span class="menu-title">
                                            @lang('menu.m_project')
                                        </span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu">

                                    <li class="dropdown-submenu">
                                        <a href="javascript:void(0);" class="have-title-proekt">
                                            <span class="have-title">
                                                <span class="menu-title">
                                                    @lang('menu.m_designing')
                                                </span>
                                            </span>
                                        </a>
                                        <ul class="dropdown-sub">

                                            <li class="menu-left-sidebar-grid">
                                                <a href="{{ url('project') }}">
                                                    <span class="have-title">
                                                        <span class="menu-title">
                                                            Общая информация
                                                        </span>
                                                    </span>
                                                </a>
                                            </li>

                                            <li class="menu-right-sidebar-grid">
                                                <a href="{{ url('project/order') }}">
                                                    <span class="have-title">
                                                        <span class="menu-title">
                                                            @lang('menu.m_designing_apply')
                                                        </span>
                                                    </span>
                                                </a>
                                            </li>

                                        </ul>
                                    </li>

                                    <li class="dropdown-submenu">
                                        <a href="{{ url('project/calculation') }}" class="have-title-proekt">
                                            <span class="have-title">
                                                <span class="menu-title">
                                                    @lang('menu.m_calculation')
                                                </span>
                                            </span>
                                        </a>
                                        <ul class="dropdown-sub">
                                            <li class="">
                                                <a href="{{ url('project/calculation/pump') }}">
                                                    <span class="have-title">
                                                        <span class="menu-title">
                                                            @lang('menu.m_calculation_pump')
                                                        </span>
                                                    </span>
                                                </a>
                                            </li>

                                            <li class="">
                                                <a href="{{ url('project/calculation/block') }}">
                                                    <span class="have-title">
                                                        <span class="menu-title">
                                                            @lang('menu.m_calculation_block')
                                                        </span>
                                                    </span>
                                                </a>
                                            </li>

                                            <li class="">
                                                <a href="{{ url('project/calculation/heat') }}">
                                                    <span class="have-title">
                                                        <span class="menu-title">
                                                            @lang('menu.m_calculation_heat')
                                                        </span>
                                                    </span>
                                                </a>
                                            </li>

                                            <li class="">
                                                <a href="{{ url('project/calculation') }}">
                                                    <span class="have-title">
                                                        <span class="menu-title">
                                                            @lang('menu.m_calculation_all')
                                                        </span>
                                                    </span>
                                                </a>
                                            </li>

                                        </ul>
                                    </li>

                                    <li class="dropdown-submenu menu-post-format">
                                        <a href="{{ url('project/tool') }}" class="have-title-proekt">
                                            <span class="have-title">
                                                <span class="menu-title">
                                                    @lang('menu.m_tool')
                                                </span>
                                            </span>
                                        </a>
                                        <ul class="dropdown-sub ">

                                            <li class="menu-post-format-video ">
                                                <a href="{{ url('project/tool/pipes') }}">
                                                    <span class="have-title">
                                                        <span class="menu-title">
                                                            @lang('menu.m_tool_pipes')
                                                        </span>
                                                    </span>
                                                </a>
                                            </li>

                                            <li class="menu-post-format-audio ">
                                                <a href="{{ url('project/tool/power') }}">
                                                    <span class="have-title">
                                                        <span class="menu-title">
                                                            @lang('menu.m_tool_power')
                                                        </span>
                                                    </span>
                                                </a>
                                            </li>

                                            <li class="menu-post-format-image ">
                                                <a href="{{ url('project/tool/scales') }}">
                                                    <span class="have-title">
                                                        <span class="menu-title">
                                                            @lang('menu.m_tool_scales')
                                                        </span>
                                                    </span>
                                                </a>
                                            </li>

                                            <li class="menu-post-format-quote ">
                                                <a href="{{ url('project/tool') }}">
                                                    <span class="have-title">
                                                        <span class="menu-title">
                                                            @lang('menu.m_tool_all')
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
                                                    @lang('menu.m_docs')
                                                </span>
                                            </span>
                                        </a>
                                        <ul class="dropdown-sub ">

                                            <li class="menu-full-width">
                                                <a href="{{ url('project/documents/gost') }}">
                                                    <span class="have-title">
                                                        <span class="menu-title">
                                                            @lang('menu.m_docs_gost')
                                                        </span>
                                                    </span>
                                                </a>
                                            </li>

                                            <li class="menu-left-sidebar">
                                                <a href="{{ url('project/documents/snip') }}">
                                                    <span class="have-title">
                                                        <span class="menu-title">
                                                            @lang('menu.m_docs_snip')
                                                        </span>
                                                    </span>
                                                </a>
                                            </li>

                                            <li class="menu-right-sidebar">
                                                <a href="{{ url('certificates/other_cert') }}">
                                                    <span class="have-title">
                                                        <span class="menu-title">
                                                            @lang('menu.m_docs_certs')
                                                        </span>
                                                    </span>
                                                </a>
                                            </li>

                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            {{-- Проект --}}

                            {{-- Монтаж --}}
                            <li class="menu-accessories flytheme-mega-menu {{ request()->is('page/mounting') ? 'active' : '' }}">
                                <a href="{{ url('page/mounting') }}" class="item-link ">
                                    <span class="have-title">
                                        <span class="menu-title">
                                            @lang('menu.m_mounting')
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- Монтаж --}}

                            {{-- Обслуживание --}}
                            <li class="menu-accessories flytheme-mega-menu {{ request()->is('page/servicing') ? 'active' : '' }}">
                                <a href="{{ url('page/servicing') }}" class="item-link ">
                                    <span class="have-title">
                                        <span class="menu-title">
                                            @lang('menu.m_servicing')
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- Обслуживание --}}

                            {{-- Сервис --}}
                            <li class="menu-accessories flytheme-mega-menu {{ request()->is('service') ? 'active' : '' }}">
                                <a href="{{ url('service') }}" class="item-link">
                                    <span class="have-title">
                                        <span class="menu-title">
                                            @lang('menu.m_service')
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- Сервис --}}

                            {{-- Контакты --}}
                            <li class="menu-accessories flytheme-mega-menu ml-auto {{ request()->is('feedback') ? 'active' : '' }}">
                                <a href="{{ url('feedback') }}" class="item-link">
                                    <span class="have-title">
                                        <span class="menu-title">
                                            @lang('menu.m_feedback')
                                        </span>
                                    </span>
                                </a>
                            </li>
                            {{-- Контакты --}}

                        </ul>
                    </div>
                </div>

            </div>
        </nav>

    </div>
</div>