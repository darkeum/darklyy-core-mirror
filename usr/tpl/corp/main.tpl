<!DOCTYPE html>
<html lang="{LANG}" xml:lang="{LANG}">

<head>
    {META}
    <meta name="theme-color" content="#535a6c">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport"
          content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <link rel="icon" href="{ADM_THEME}/assets/images/favicon.ico" type="image/x-icon">

    <!-- Stylesheets-->
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/admin-forms.css">
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/summernote.css">
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/bootstrap-switch.min.css">
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/octicons.css">
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/magnific-popup.css">
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/typeahead.js-bootstrap.css">
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/theme.css">
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/tagmanager.css">
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/bootstrap-colorpicker.min.css">
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/core.css">
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/informers.css">
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/bootsarp-4_border.css">
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/bootsarp-4_font.css">
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/bootsarp-4_display.css">
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/bootsarp-4_flex.css">

    <script src="{ADM_THEME}/assets/js/jquery.min.js"></script>

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script src="{ADM_THEME}/assets/js/jquery-migrate-301.min.js"></script>
    <script src="{ADM_THEME}/assets/js/jquery-ui.js"></script>
    <script src="/usr/plugins/js/js.cookie.js"></script>
    <script src="{ADM_THEME}/assets/js/script.js?240120201050"></script>

    <!-- Core scripts-->
    <script src="{ADM_THEME}/assets/js/core.min.js"></script>
    <script src="{ADM_THEME}/assets/js/offline.min.js"></script>
    <script src="{ADM_THEME}/assets/js/snake.js"></script>
    <script src="{ADM_THEME}/assets/js/utility.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"
            integrity="sha256-1A78rJEdiWTzco6qdn3igTBv9VupN3Q1ozZNTR4WE/Y=" crossorigin="anonymous"></script>

    <!-- Fonts-->
    <link rel="stylesheet" type="text/css"
          href="//fonts.googleapis.com/css?family=Lato:300,400,600,700%7CMontserrat:400,700">
    <link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/fonts/font-awesome-pro/css/all.min.css">
    <title>Атри-Энерго</title>
    <!--[if lt IE 10]>
		<div style="background: #212121; padding: 10px 0; box-shadow: 3px 3px 5px 0 rgba(0,0,0,.3); clear: both; text-align:center; position: relative; z-index:1;"><a href="http://windows.microsoft.com/en-US/internet-explorer/"><img src="{ADM_THEME}/assets/images/ie8-panel/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today."></a></div>
		<script src="{ADM_THEME}/assets/js/html5shiv.min.js"></script>
	<![endif]-->
</head>

<body data-spy="scroll" data-target="#nav-spy" data-offset="300" class="form-editors-page online {BODY_CLASS} {NOW_COMPANY}">
<div class="overlay-offline"></div>
<div id="main">
    <header class="navbar navbar-fixed-top bg-dark">
        <div class="navbar-branding light bg-danger">
            <a href="{ADMIN}" class="navbar-brand text-uppercase">
                <img style="width: 170px; padding-left: 5px;" src="{ADM_THEME}/assets/images/logo-{NOW_COMPANY}.png" alt="logo">
            </a>
            <span id="toggle_sidemenu_l" class="fa fa-bars"></span>
        </div>
        <ul class="nav navbar-nav navbar-left"></ul>
        <form role="search" method="POST" action="{ADMIN}/search"
              class="nophone navbar-form navbar-left navbar-search alt">
            <span class="hide visible-md-inline-block visible-lg-inline-block fa fa-search fs18"></span>
            <div class="form-group">
                <input type="text" name="search" placeholder="[alang_old:_PANEL_SEARCH]" class="form-control">
            </div>
        </form>

        <ul class="nav navbar-nav navbar-right">
            <li class="nophone">
                <div class="navbar-btn btn-group">
                    <a href="{URL}" target="_blank" class="btn btn-sm" title="Перейти на сайт"><span
                                class="fa fa-globe fs15"></span></a>
                </div>
            </li>
            <li class="nophone">
                <div class="navbar-btn btn-group">
                    <a href="javascript:void(0);" class="btn btn-sm AddProduct" onclick="{VICTORIA_CLICK}"
                       title="Быстрый подбор">
                        <span class="fal fa-cart-arrow-down fs15"></span>
                    </a>
                </div>
            </li>
            <li class="nophone">
                <div class="navbar-btn btn-group">
                    <button class="btn btn-sm request-fullscreen" title="Во весь экран"><span
                                class="fa fa-arrows-alt fs15"></span></button>
                </div>
            </li>
            <li>
                <div class="navbar-btn btn-group">
                    <a href="{URL_FULL}#" class="topbar-menu-toggle btn btn-sm"><span class="fa fa-magic"></span></a>
                </div>
            </li>
            <li class="dropdown menu-merge mr20">
                <div class="navbar-btn btn-group">
                    <button data-toggle="dropdown" class="btn btn-sm dropdown-toggle">
                        <span class="fas fa-bell fs14 va-m"></span>
                        <span class="badge badge-danger">{NOTIF_NUMB}</span>
                    </button>
                    <div role="menu" class="dropdown-menu dropdown-persist w350 animated animated-shorter fadeIn">
                        <div class="panel mbn">
                            <div class="panel-menu"><span class="panel-icon"><i class="fa fa-clock-o"></i></span><span
                                        class="panel-title fw600"> [alang_old:_PANEL_SUNMENU_NOTIF]</span></div>
                            <div class="w100p panel-body panel-scroller scroller-navbar scroller-overlay scroller-pn pn">
                                <ol class="timeline-list">
                                    {NOTIF}
                                </ol>
                            </div>
                            <div class="panel-footer text-center p7"><a href="{ADMIN}/module/notif"
                                                                        class="link-unstyled"> Смотреть Все</a></div>
                        </div>
                    </div>
                </div>
            </li>
            <li><img width="70" height="70" src="{AVATAR}" alt="avatar"></li>
            <li class="dropdown menu-merge">
                <a href="javascript:;" data-toggle="dropdown" class="dropdown-toggle fw600 p15">
                    <span class="fa fa-angle-down"></span>
                </a>
                <ul role="menu" class="dropdown-menu list-group dropdown-persist w250">
                    <li class="dropdown-header clearfix">
                        <div class="pull-left ml10">
                            <select id="user-status">
                                <optgroup label="[alang_old:_PANEL_SUNMENU_STATUS]">

                                </optgroup>
                            </select>
                        </div>
                        <div class="pull-right mr10">
                            <div class="btn-group">
                                <button type="button" class="multiselect dropdown-toggle btn btn-default btn-sm"
                                        data-toggle="dropdown" style="max-width: 100px;" aria-expanded="false">
                                    [alang_old:_PANEL_SUNMENU_VIEW] <b class="caret-right"></b></button>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item"><a href="pm" class="animated animated-short fadeInUp"><span
                                    class="fa fa-envelope"></span> Сообщения<span
                                    class="label label-warning">{MESSAGES_NUMB}</span></a></li>
                    <li class="list-group-item"><a href="profile" class="animated animated-short fadeInUp"><span
                                    class="fa fa-user"></span> Друзья<span
                                    class="label label-warning">{FRIENDS_NUMB}</span></a></li>
                    <li class="list-group-item"><a href="profile" class="animated animated-short fadeInUp"><span
                                    class="fa fa-bell"></span> Профиль</a></li>
                    <li class="list-group-item"><a href="{ADMIN}/config" class="animated animated-short fadeInUp"><span
                                    class="fa fa-gear"></span> Настройки</a></li>
                    <li class="dropdown-footer">
                        <a href="profile/logout"><span class="fa fa-power-off pr5"></span>
                            [alang_old:_PANEL_SUNMENU_EXIT]</a>
                    </li>
                </ul>
            </li>
        </ul>
        <div class="currency-bar nav navbar-nav navbar-right nophone" style="position: relative; top: 5px;">
            <!-- <iframe style="width:300px;border:0;overflow:hidden;background-color:transparent;height:80px" scrolling="no" src="https://fortrader.org/informers/getInformer?st=17&cat=7&title=&texts=%7B%22toolTitle%22%3A%22%D0%92%D0%B0%D0%BB%D1%8E%D1%82%D0%B0%22%2C%22todayCourse%22%3A%22RUB%22%7D&mult=1&showGetBtn=0&hideHeader=1&hideDate=1&w=400&codes=1&colors=titleTextColor%3D454545%2CtitleBackgroundColor%3D3b3f4f%2CsymbolTextColor%3D444444%2CtableTextColor%3Dffffff%2CborderTdColor%3D3b3f4f%2CtableBorderColor%3D3b3f4f%2CtrBackgroundColor%3D3b3f4f%2CitemImgBg%3DF1F1F1%2CprofitTextColor%3Db6d7a8%2CprofitBackgroundColor%3Deaf7e1%2ClossTextColor%3Dea9999%2ClossBackgroundColor%3Df6e1e1%2CinformerLinkTextColor%3D454242%2CinformerLinkBackgroundColor%3Dfff&items=2%2C21&columns=&toCur=11111"></iframe>	 -->
            <div class="price-bar">
                <div class="priceContainer priceContainer-mr">
                    <div class="symbolContainer symbolContainer-mr" title="Доллар США">$</div>
                    <div class="amount_middle">
                        <div class="amount_middle__above">{PRICE_US}</div>
                        <div class="amount_middle__under {PRICE_US_DIS_COLOR}">{PRICE_US_DIS}</div>
                    </div>
                </div>
                <div class="priceContainer">
                    <div class="symbolContainer symbolContainer-mr" title="Евро">€</div>
                    <div class="amount_middle">
                        <div class="amount_middle__above">{PRICE_EUR}</div>
                        <div class="amount_middle__under {PRICE_EUR_DIS_COLOR}">{PRICE_EUR_DIS}</div>
                    </div>
                </div>
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
            <ul class="nav sidebar-menu">
                <li {MENU_CHOOSE:main}>
                    <a href="{ADMIN}"><span class="glyphicon glyphicon-home"></span><span class="sidebar-title">[alang_old:_PANEL_MENU_MAIN]</span></a>
                </li>
                <li {MENU_CHOOSE:task}>
                    <a href="{ADMIN}/module/task"><span class="glyphicon glyphicon-ok-sign"></span><span
                                class="sidebar-title">Задачи</span> <span class="badge badge-danger">{TASK_COUNT}</span></a>
                </li>
                <li>
                    <a href="javascript:;" class="accordion-toggle {MENU_OPEN:sales,realization,boiler,form}">
                        <span class="glyphicon glyphicon-shopping-cart"></span>
                        <span class="sidebar-title">Заказы</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {MENU_CHOOSE_MODULE:boiler:bid}>
                            <a href="{ADMIN}/module/boiler/bid"><span class="fas fa-cauldron"></span>Котел</a>
                        </li>
                        <li {MENU_CHOOSE:sales}>
                            <a href="{ADMIN}/module/sales"><span class="fa fa-book"></span>Все счета</a>
                        </li>
                        <li {MENU_CHOOSE:realization}>
                            <a href="{ADMIN}/module/realization"><span class="fa fa-rub"></span>Документы продажи</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:realization:correct}>
                            <a href="{ADMIN}/module/realization/correct"><span class="far fa-edit"></span>Документы
                                корректировки</a>
                        </li>
                        <li {MENU_CHOOSE:form}>
                            <a href="{ADMIN}/module/form"><span class="fa fa-calculator"></span>Подбор оборудования</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;"
                       class="accordion-toggle {MENU_OPEN:client,organizations,calls,order,select,profile}">
                        <span class="glyphicon glyphicon-user"></span>
                        <span class="sidebar-title">Клиенты</span>
                        <span class="caret"></span>
                        <span class="badge badge-danger">{CLIENT_COUNT}</span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {MENU_CHOOSE:calls}>
                            <a href="{ADMIN}/calls"><span class="far fa-phone"></span>Звонки</a>
                        </li>
                        <li {MENU_CHOOSE:order}>
                            <a href="{ADMIN}/module/order"><span class="far fa-comment-alt-check"></span>Заявки и
                                обращения <span class="badge badge-danger">{ORDER_COUNT}</span></a>
                        </li>
                        <li {MENU_CHOOSE:organizations}>
                            <a href="{ADMIN}/module/organizations"><span class="far fa-building"></span>Контрагенты</a>
                        </li>
                        <li {MENU_CHOOSE:client}>
                            <a href="{ADMIN}/client"><span class="far fa-users"></span>Контактные лица</a>
                        </li>
                        <li {MENU_CHOOSE:profile}>
                            <a href="{ADMIN}/module/profile"><span class="far fa-gift"></span>Новогодние подарки</a>
                        </li>
                        <li {MENU_CHOOSE:promotions}>
                            <a href="{ADMIN}/module/promotions"><span class="far fa-gift"></span>Акции</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;" class="accordion-toggle {MENU_OPEN:supply,logistics}">
                        <span class="fa fa-truck"></span>
                        <span class="sidebar-title">Закупки</span>
                        <span class="caret"></span>
                        <span class="badge badge-danger">{SUPPLY_COUNT}</span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {MENU_CHOOSE_MODULE:supply:main,detail}>
                            <a href="{ADMIN}/module/supply"><span class="fa fa-check-square"></span>Заявки на закупку
                                <span class="badge badge-danger">{SUPPLY_COUNT_TYPE_1}</span></a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:supply:supply_stock}>
                            <a href="{ADMIN}/module/supply/supply_stock"><span class="fa fa-check-square"></span>Заявки
                                на закупку(склад)</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:supply:twomain,twodetail}>
                            <a href="{ADMIN}/module/supply/twomain"><span class="fa fa-clock-o"></span>Заявки на сроки
                                <span class="badge badge-danger">{SUPPLY_COUNT_TYPE_2}</span></a>

                        </li>
                        <li {MENU_CHOOSE_MODULE:supply:order}>
                            <a href="{ADMIN}/module/supply/order"><span class="far fa-file-text"></span>Счета
                                поставщиков</a>
                        </li>
                        <!-- <li {MENU_CHOOSE_MODULE:supply:logistics}>
						<a href="{ADMIN}/module/supply/logistics"><span class="fa fa-truck"></span>Логистика <span class="badge badge-danger">{SUPPLY_COUNT_TYPE_3}</span></a>
					</li> -->
                        <li {MENU_CHOOSE:logistics}>
                            <a href="{ADMIN}/module/logistics"><span class="fa fa-truck"></span>Логистика</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:supply:logistics_receipts}>
                            <a href="{ADMIN}/module/supply/logistics_receipts"><span
                                        class="fas fa-truck-loading"></span>Приемка груза</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:supply:receipts}>
                            <a href="{ADMIN}/module/supply/receipts"><span class="fa fa-book"></span>Документы
                                поступления</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;"
                       class="accordion-toggle {MENU_OPEN:stock,barcode,storage,merging,delivery_storage,}">
                        <span class="fa fa-archive"></span>
                        <span class="sidebar-title">Склад</span>
                        <span class="caret"></span>
                        <span class="badge badge-danger">{STORAGE_COUNT}</span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {MENU_CHOOSE_MODULE:storage:main}>
                            <a href="{ADMIN}/module/storage"><span class="fa fa-dot-circle-o "></span>Заявки на пересчет
                                <span class="badge badge-danger">{RECOUNT_COUNT}</span></a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:storage:assembly}>
                            <a href="{ADMIN}/module/storage/assembly"><span class="fas fa-box-open "></span>Заявки на
                                сборку <span class="badge badge-danger">{ASSEMBLY_COUNT}</span></a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:storage:docPrint}>
                            <a href="{ADMIN}/module/storage/docPrint"><span class="fal fa-ballot-check"></span>Разрешенная
                                отгрузка </a>
                        </li>
                        <li {MENU_CHOOSE:merging}>
                            <a href="{ADMIN}/module/merging"><span class="fal fa-box-check"></span>Комплектация товаров</a>
                        </li>
                        <!-- <li {MENU_CHOOSE_MODULE:storage:reserv}>
						<a href="{ADMIN}/module/storage/reserv"><span class="fas fa-lock-alt"></span>Менеджер резервов </a>
					</li>	 -->
                        <li {MENU_CHOOSE_MODULE:storage:transfer}>
                            <a href="{ADMIN}/module/storage/transfer"><span class="fa fa-retweet"></span>Перемещение
                                товаров <span class="badge badge-danger">{TRANSFER_COUNT}</span></a>
                        </li>
                        <li {MENU_CHOOSE:stock}>
                            <a href="{ADMIN}/stock"><span class="fa fa-archive "></span>Наличие товаров</a>
                        </li>
                        <li {MENU_CHOOSE:barcode}>
                            <a href="{ADMIN}/module/barcode"><span class="fa fa-qrcode"></span>Штрихкоды</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:storage:delivery_storage}>
                            <a href="{ADMIN}/module/storage/delivery_storage"><span class="fa fa-truck"></span>Доставка
                                <span class="badge badge-danger">{DELIVERY_STORAGE}</span></a>
                        </li>
                        <li {MENU_CHOOSE:warranties}>
                            <a href="{ADMIN}/module/warranties"><span class="far fa-id-card"></span>Гарантийный
                                талон</a>
                        </li>

                    </ul>
                </li>
                <li>
                    <a href="javascript:;" class="accordion-toggle {MENU_OPEN:production}">
                        <span class="fas fa-industry-alt"></span>
                        <span class="sidebar-title">Производство</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {MENU_CHOOSE:production}>
                            <a href="{ADMIN}/module/production"><span class="far fa-cabinet-filing"></span>Реестр
                                изделий</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;" class="accordion-toggle {MENU_OPEN:requests,selection}">
                        <span class="fas fa-drafting-compass"></span>
                        <span class="sidebar-title">Проектирование</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {MENU_CHOOSE:requests}>
                            <a href="{ADMIN}/module/requests"><span class="far fa-envelope-open-text"></span>Заявки на
                                расчет</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:selection:request}>
                            <a href="{ADMIN}/module/selection/request"><span class="fas fa-cubes"></span>Заявки на
                                БТП</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:selection:main} {MENU_CHOOSE_MODULE:selection:detail}>
                            <a href="{ADMIN}/module/selection"><span class="fas fa-box-open"></span>Блоки</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:selection:metering} {MENU_CHOOSE_MODULE:selection:metering_detail}>
                            <a href="{ADMIN}/module/selection/metering"><span class="fas fa-tachometer-slowest"></span>Узлы
                                учета</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:engineering:register} {MENU_CHOOSE_MODULE:engineering:register}>
                            <a href="{ADMIN}/module/engineering/register"><span
                                        class="fas fa-tachometer-slowest"></span>Регистр</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:project:blocks}>
                            <a href="{ADMIN}/module/project/blocks"><span class="fas fa-th"></span>Редактор блоков</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;" class="accordion-toggle {MENU_OPEN:fcontrol}">
                        <span class="glyphicon glyphicon-credit-card"></span>
                        <span class="sidebar-title">Оплата и контроль</span>
                        <span class="caret"></span>
                        <span class="badge badge-danger">{FCONTROL_ALL}</span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {MENU_CHOOSE_MODULE:fcontrol:main}>
                            <a href="{ADMIN}/module/fcontrol"><span class="glyphicon glyphicon-ok-circle"></span>Контроль
                                закупок <span class="badge badge-danger">{FCONTROL}</span></a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:fcontrol:storage}>
                            <a href="{ADMIN}/module/fcontrol/storage"><span class="glyphicon glyphicon-random"></span>Контроль
                                остатков</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:fcontrol:pay}>
                            <a href="{ADMIN}/module/fcontrol/pay"><span class="glyphicon glyphicon-usd"></span>Заявки
                                оплату <span class="badge badge-danger">{FCONTROL_PAY}</span></a>
                        </li>
                        <li {MENU_CHOOSE:god}>
                            <a href="{ADMIN}/god"><span class="fas fa-users-crown"></span>Управление остатками</a>
                        </li>
                    </ul>
                </li>
                <li {MENU_CHOOSE:verification}>
                    <a href="{ADMIN}/module/verification"><span class="fa fa-dashboard"></span><span
                                class="sidebar-title">Поверка приборов</span></a>
                </li>
                <li {MENU_CHOOSE:object}>
                    <a href="{ADMIN}/object"><span class="glyphicon glyphicon-briefcase"></span><span
                                class="sidebar-title">Объекты</span></a>
                </li>

                <li>
                    <a href="javascript:;" class="accordion-toggle {MENU_OPEN:catalog_v2,price,cats,groups,catalog}">
                        <span class="glyphicon glyphicon-th-large"></span>
                        <span class="sidebar-title">Каталог товаров</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {MENU_CHOOSE_MODULE:catalog:main}>
                            <a href="{ADMIN}/module/catalog"><span class="fa fa-list"></span>Список товаров</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:catalog:excel}>
                            <a href="{ADMIN}/module/catalog/excel"><span class="far fa-file-excel"></span>Работа с
                                товарами через Excel</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:catalog_v2:cert}>
                            <a href="{ADMIN}/module/catalog_v2/cert"><span class="glyphicon glyphicon-ok-circle"></span>Сертификаты
                                товаров</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:price:main}>
                            <a href="{ADMIN}/module/price"><span class="glyphicon glyphicon-usd"></span>Цены
                                номенклатуры</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:catalog:attribute}>
                            <a href="{ADMIN}/module/catalog/attribute"><span class="fas fa-tags"></span>Редактор
                                атрибутов</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:catalog_v2:file_upload}>
                            <a href="{ADMIN}/module/catalog_v2/file_upload"><span class="far fa-images"></span>Загрузка
                                изображений</a>
                        </li>
                        <li {MENU_CHOOSE:cats}>
                            <a href="{ADMIN}/cats">
                                <span class="glyphicon glyphicon-list"></span>
                                <span class="sidebar-title">[alang_old:_PANEL_MENU_NEWS_CAT]  </span>
                            </a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:catalog_v2:groups}>
                            <a href="{ADMIN}/module/catalog_v2/groups"><span class="far fa-images"></span>Группы товаров</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:price:catalog_price_rival}>
                            <a href="{ADMIN}/module/price/catalog_price_rival"><span class="far fa-images"></span>Настройка
                                цены</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:price:cpdo}>
                            <a href="{ADMIN}/module/price/cpdo"><span class="far fa-images"></span>Привязка скидок
                                клиентам</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:price:cpdo:groups}>
                            <a href="{ADMIN}/module/price/cpdo/groups"><span class="far fa-images"></span>Привязка
                                скидок клиентам по группе</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:price:catalog_price_rival:discount}>
                            <a href="{ADMIN}/module/price/catalog_price_rival/discount"><span
                                        class="far fa-images"></span>Привязка скидок конкурентов</a>
                        </li>
                    </ul>
                </li>

                <li {MENU_CHOOSE:cashbox}>
                    <a href="{ADMIN}/module/cashbox"><span class="fas fa-cash-register"></span><span
                                class="sidebar-title">Касса</span></a>
                </li>
             

                <li>
                    <a href="javascript:;"
                       class="accordion-toggle {MENU_OPEN:reestr,decrees,user,groups,nomenclature,bank,inventory,doc,access,economic}">
                        <span class="fa fa-building"></span>
                        <span class="sidebar-title">Администрирование</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {MENU_CHOOSE:reestr}>
                            <a href="{ADMIN}/module/reestr"><span class="fas fa-books"></span>Реестр договоров</a>
                        </li>
                        <li {MENU_CHOOSE:decrees}>
                            <a href="{ADMIN}/module/decrees"><span class="fas fa-book"></span>Реестр приказов</a>
                        </li>
                        <li {MENU_CHOOSE:bank}>
                            <a href="{ADMIN}/module/bank"><span class="fas fa-rub"></span>Банковская выписка</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:economic:act}>
                            <a href="{ADMIN}/module/economic/act"><span class="fas fa-id-card-alt"></span>Акт сверки</a>
                        </li>
                        <li {MENU_CHOOSE:inventory}>
                            <a href="{ADMIN}/module/inventory"><span class="fas fa-qrcode"></span>Инвентаризация</a>
                        </li>
                        <li {MENU_CHOOSE:orgs}>
                            <a href="{ADMIN}/module/orgs"><span class="fas fa-building"></span>Изменение организаций</a>
                        </li>
                        <li {MENU_CHOOSE:stocks}>
                            <a href="{ADMIN}/module/stocks"><span class="fas fa-warehouse"></span>Склады организаций</a>
                        </li>
                       
                    </ul>
                </li>
                <li {MENU_CHOOSE:report}>
                    <a href="{ADMIN}/module/report"><span class="fa fa-chart-pie"></span><span class="sidebar-title">Отчёты</span></a>
                </li>
                <!-- <li>
                        <a href="javascript:;" class="accordion-toggle {MENU_OPEN:report}">
                            <span class="fa fa-chart-pie"></span>
                            <span class="sidebar-title">Отчёты</span>
                            <span class="caret"></span>
                        </a>
                        <ul class="nav sub-nav">
                            <li {MENU_CHOOSE:report:storage}>
                                <a href="{ADMIN}/module/report/storage"><span class="fas fa-file-contract"></span>Отчёт по движению товара</a>
                            </li>
                            <li {MENU_CHOOSE:report:storage_no_trans}>
                                <a href="{ADMIN}/module/report/storage_no_trans"><span class="fas fa-file-contract"></span>Отчёт по фактическим остаткам</a>
                            </li>
                            <li {MENU_CHOOSE:report:realization}>
                                <a href="{ADMIN}/module/report/realization"><span class="fas fa-file-contract"></span>Отчёт по реализациям</a>
                            </li>
                            <li {MENU_CHOOSE:report:realization_catalog}>
                                <a href="{ADMIN}/module/report/realization_catalog"><span class="fas fa-file-contract"></span>Отчёт по реализации товаров</a>
                            </li>
                            <li {MENU_CHOOSE:report:realization_catalog}>
                                <a href="{ADMIN}/module/report/realization_all_catalog"><span class="fas fa-file-contract"></span>Отчёт по реализации товаров(подробный)</a>
                            </li>
                            <li {MENU_CHOOSE:report:shipment_minus}>
                                <a href="{ADMIN}/module/report/shipment_minus"><span class="fas fa-file-contract"></span>Отчёт по отгрузке товаров в минус</a>
                            </li>
                            <li {MENU_CHOOSE:report:storage_sum}>
                                <a href="{ADMIN}/module/report/storage_sum"><span class="fas fa-file-contract"></span>Отчёт по сумме на складах</a>
                            </li>
                            <li {MENU_CHOOSE:report:storage_sum}>
                                <a href="{ADMIN}/module/report/average_delivery_time"><span class="fas fa-file-contract"></span>Отчёт по средней времени доставки</a>
                            </li>
                            <li {MENU_CHOOSE:report:merging}>
                                <a href="{ADMIN}/module/report/merging"><span class="fas fa-file-contract"></span>Отчёт по комплектации</a>
                            </li>
                        </ul>
                    </li> -->
                <li>
                    <a href="javascript:;"
                       class="accordion-toggle {MENU_OPEN:news,fm,xfields,comments,moderation,voting,board,content,modules,blocks,templates,guestbook,partner,project,certificates,pto,reviews,job,portfolio}">
                        <span class="glyphicon glyphicon-globe"></span>
                        <span class="sidebar-title">Управление сайтом</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {MENU_CHOOSE:news}>
                            <a href="{ADMIN}/module/news">
                                <span class="far fa-newspaper"></span>Менеджер новостей
                            </a>
                        </li>
                         <li {MENU_CHOOSE:away}>
                            <a href="{ADMIN}/module/away">
                                <span class="far fa-link"></span>Сокращение ссылок
                            </a>
                        </li>
                        <li {MENU_CHOOSE:news}>
                            <a href="{ADMIN}/module/job">
                                <span class="far fa-user-plus"></span>Менеджер вакансий
                            </a>
                        </li>
                        <li {MENU_CHOOSE:portfolio}>
                            <a href="{ADMIN}/module/portfolio">
                                <span class="far fa-briefcase"></span>Менеджер портфолио
                            </a>
                        </li>
                        <li {MENU_CHOOSE:reviews}>
                            <a href="{ADMIN}/module/reviews">
                                <span class="far fa-comment-alt-dots"></span>Отзывы клиентов
                            </a>
                        </li>
                        <li {MENU_CHOOSE:certificates}>
                            <a href="{ADMIN}/module/certificates"><span class="far fa-file-certificate"></span>Менеджер
                                сертификатов</a>
                        </li>


                        <li>
                            <a href="javascript:;"
                               class="accordion-toggle {MENU_OPEN:comments,moderation,voting,guestbook}">
                                <span class="glyphicon glyphicon-user"></span>
                                <span class="sidebar-title">[alang_old:_PANEL_MENU_USER]</span>
                                <span class="caret"></span>
                            </a>
                            <ul class="nav sub-nav">
                                <li {MENU_CHOOSE:comments}>
                                    <a href="{ADMIN}/comments"><span class="glyphicon glyphicon-comment"></span>[alang_old:_PANEL_MENU_USER_COMMENT]</a>
                                </li>
                                <li {MENU_CHOOSE:moderation}>
                                    <a href="{ADMIN}/moderation"><span class="glyphicon glyphicon-edit"></span>[alang:moder]</a>
                                </li>
                                <li {MENU_CHOOSE:voting}>
                                    <a href="{ADMIN}/voting"><span class="glyphicon glyphicon-plus-sign"></span>[alang_old:_PANEL_MENU_USER_POLL]</a>
                                </li>
                                [ACTIVE_MODULE:guestbook]
                                <li {MENU_CHOOSE:guestbook}>
                                    <a href="{ADMIN}/module/guestbook"><span class="far fa-comment-alt-dots"></span>Гостевая
                                        книга</a>
                                </li>
                                [/ACTIVE_MODULE]
                            </ul>
                        </li>
                        <li>
                            <a href="javascript:;"
                               class="accordion-toggle {MENU_OPEN:board,content,partner,project,certificates,pto}">
                                <span class="glyphicon glyphicon-th-large"></span>
                                <span class="sidebar-title">[alang_old:_PANEL_MENU_COM]</span>
                                <span class="caret"></span>
                            </a>
                            <ul class="nav sub-nav">
                                [ACTIVE_MODULE:board]
                                <li {MENU_CHOOSE:board}>
                                    <a href="{ADMIN}/module/board"><span class="fas fa-bullhorn"></span>[alang_old:_PANEL_MENU_COM_FORUM]</a>
                                </li>
                                [/ACTIVE_MODULE] [ACTIVE_MODULE:content]
                                <li {MENU_CHOOSE:content}>
                                    <a href="{ADMIN}/module/content"><span class="far fa-sticky-note"></span>[alang_old:_PANEL_MENU_COM_STATIC]</a>
                                </li>
                                [/ACTIVE_MODULE] [ACTIVE_MODULE:partner]
                                <li {MENU_CHOOSE:partner}>
                                    <a href="{ADMIN}/module/partner"><span class="far fa-handshake"></span>Менеджер
                                        партнёров</a>
                                </li>
                                [/ACTIVE_MODULE] [ACTIVE_MODULE:pto]
                                <li {MENU_CHOOSE:pto}>
                                    <a href="{ADMIN}/module/pto"><span class="fas fa-layer-group"></span>Менеджер
                                        теплообменников</a>
                                </li>
                                [/ACTIVE_MODULE]
                            </ul>
                        </li>

                        <li>
                            <a href="javascript:;" class="accordion-toggle {MENU_OPEN:modules,blocks,templates}">
                                <span class="glyphicon glyphicon-send"></span>
                                <span class="sidebar-title">[alang_old:_PANEL_MENU_EXP]</span>
                                <span class="caret"></span>
                            </a>
                            <ul class="nav sub-nav">
                                <li {MENU_CHOOSE:modules}>
                                    <a href="{ADMIN}/modules"><span class="fa fa-cloud"></span>[alang_old:_PANEL_MENU_EXP_MODULES]</a>
                                </li>
                                <li {MENU_CHOOSE:blocks}>
                                    <a href="{ADMIN}/blocks"><span class="fa fa-code"></span>[alang_old:_PANEL_MENU_EXP_BLOCKS]</a>
                                </li>
                                <li {MENU_CHOOSE:templates}>
                                    <a href="{ADMIN}/templates"><span class="fa fa-pencil-square-o"></span>[alang_old:_PANEL_MENU_EXP_TPL]</a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </li>
                <li>
                    <a href="javascript:;" class="accordion-toggle {MENU_OPEN:files}">
                        <span class="fas fa-folder-open"></span>
                        <span class="sidebar-title">Файловый менеджер</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {MENU_CHOOSE_MODULE:files:server}>
                            <a href="{ADMIN}/module/files/server"><span class="fa fa-list"></span>Серверное
                                хранилище</a>
                        </li>
                        <li {MENU_CHOOSE_MODULE:files:project}>
                            <a href="{ADMIN}/module/files/project"><span class="glyphicon glyphicon-ok-circle"></span>Проектирование</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;"
                       class="accordion-toggle {MENU_OPEN:sitemap,smiles,db,log,update,changelog,sync,daemon,dev}">
                        <span class="glyphicon glyphicon-question-sign"></span>
                        <span class="sidebar-title">[alang_old:_PANEL_MENU_OTHER]</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="nav sub-nav">
                        <li {MENU_CHOOSE:update}>
                            <a href="{ADMIN}/module/object"><span class="glyphicon glyphicon-briefcase"></span><span
                                        class="sidebar-title">Объекты</span></a>
                        </li>
                        <li {MENU_CHOOSE:config}>
                            <a href="{ADMIN}/config">
                                <span class="glyphicon glyphicon-cog"></span>
                                <span class="sidebar-title">[alang_old:_PANEL_MENU_CONFIG]  </span>
                            </a>
                        </li>
                        <li {MENU_CHOOSE:changelog}>
                            <a href="{ADMIN}/module/changelog"><span class="fa  fa-bullhorn"></span>История
                                изменений</a>
                        </li>
                        <li {MENU_CHOOSE:sync}>
                            <a href="{ADMIN}/module/sync"><span class="glyphicon  glyphicon-refresh"></span>Синхронизация
                                данных</a>
                        </li>
                        <li {MENU_CHOOSE:log}>
                            <a href="{ADMIN}/log"><span class="glyphicon glyphicon-info-sign"></span>[alang_old:_PANEL_MENU_OTHER_LOG]</a>
                        </li>
                        <li {MENU_CHOOSE:update}>
                            <a href="{ADMIN}/update"><span class="glyphicon glyphicon-download"></span>[alang_old:_PANEL_MENU_OTHER_UPDATE]</a>
                        </li>
                        <li {MENU_CHOOSE:developers}>
                            <a href="{ADMIN}/module/developers"><span class="glyphicon  glyphicon-refresh"></span>Для разработчиков</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-label pt35"></li>
                <li {MENU_CHOOSE:support}><a href="{ADMIN}/module/support"><span
                                class="glyphicon glyphicon-bullhorn"></span><span class="sidebar-title">Поддержка</span></a>
                </li>
                <li {MENU_CHOOSE:documentation}><a href="{ADMIN}/module/documentation"><span
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
                    <a href="{ADMIN}/calendar" class="metro-tile bg-primary light">
                        <span class="glyphicon glyphicon-calendar text-muted"></span>
                        <span class="metro-title">Календарь</span>
                    </a>
                </div>
                <div class="col-xs-4 col-sm-2">
                    <a href="{ADMIN}/module/sales" class="metro-tile bg-info light">
                        <span class="glyphicon glyphicon-calendar text-muted"></span>
                        <span class="metro-title">Продажи</span>
                    </a>
                </div>

                <div class="col-xs-4 col-sm-2">
                    <a href="{ADMIN}/user" class="metro-tile bg-info light">
                        <span class="glyphicon glyphicon-user text-muted"></span>
                        <span class="metro-title">[alang_old:_PANEL_MENU_USER]</span>
                    </a>
                </div>
                [ACTIVE_MODULE:content]
                <div class="col-xs-4 col-sm-2">
                    <a href="{ADMIN}/module/content" class="metro-tile bg-success light">
                        <span class="glyphicon glyphicon-edit text-muted"></span>
                        <span class="metro-title">[alang_old:_PANEL_MENU_COM_STATIC]</span>
                    </a>
                </div>
                [/ACTIVE_MODULE] [ACTIVE_MODULE:board]
                <div class="col-xs-4 col-sm-2">
                    <a href="{ADMIN}/module/board" class="metro-tile bg-system light">
                        <span class="glyphicon glyphicon-comment text-muted"></span>
                        <span class="metro-title">[alang_old:_PANEL_MENU_COM_FORUM]</span>
                    </a>
                </div>
                [/ACTIVE_MODULE] [ACTIVE_MODULE:gallery]
                <div class="col-xs-4 col-sm-2">
                    <a href="{ADMIN}/module/gallery" class="metro-tile bg-alert light">
                        <span class="glyphicon glyphicon-picture text-muted"></span>
                        <span class="metro-title">[alang_old:_PANEL_MENU_COM_GALLERY]</span>
                    </a>
                </div>
                [/ACTIVE_MODULE]
                <div class="col-xs-4 col-sm-2">
                    <a href="{ADMIN}/config" class="metro-tile bg-warning light">
                        <span class="fa fa-gears text-muted"></span>
                        <span class="metro-title">[alang_old:_PANEL_MENU_CONFIG]</span>
                    </a>
                </div>
            </div>
        </div>
        [CHECK_ACTIVE]
        <header id="topbar" class="text-center bg-white alt ph10 br-b-ddd">
            <div>
                {SUBNAV}
            </div>
            {TOPBAR}
        </header>
        [/CHECK_ACTIVE] {BIRTHDAY} {MODULE}
    </section>
</div>
<style>
    .btn-toolbar > .btn-group.note-fontname {
        display: none;
    }
</style>

<!-- Theme Javascript-->


<script src="{ADM_THEME}/assets/js/demo.js"></script>
<script src="{ADM_THEME}/assets/js/main.js"></script>
<script src="{ADM_THEME}/assets/js/widgets.js"></script>
<script src="{ADM_THEME}/assets/js/sweetalert.min.js"></script>

<script src="{ADM_THEME}/assets/js/bootstrap-switch.min.js"></script>
<script src="{ADM_THEME}/assets/js/jquery.qrcode.min.js"></script>
<script src="{ADM_THEME}/assets/js/jquery.smartWizard.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css"/>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/offline-language-english.css">
<link rel="stylesheet" type="text/css" href="{ADM_THEME}/assets/css/offline-theme-dark.css">

<script src="/usr/other/sub/load_window/asset/js/load_window.js"></script>

{FOOT}

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
        {JS_CODE}
    });
</script>
<script>
    var run = function () {
        if (Offline.state === 'up')
            Offline.check();
    }
    setInterval(run, 1000);
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
</body>

</html>