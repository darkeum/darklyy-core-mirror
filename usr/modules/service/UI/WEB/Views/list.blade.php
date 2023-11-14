@extends('app')

@section('content')
    <div id="wrapper">
        <div class="main-container">
            <div class="container">
                <div class="row">

                    <div id="shopify-section-blog-sidebar"
                         class="shopify-section blog-sidebar col-sidebar sidebar"
                    >
                        @include('layouts.sidebar', [
                            'categories' => $categories,
                            'url' => 'service'
                        ])
                    </div>

                    <div id="shopify-section-blog-template" class="shopify-section">

                        {{-- Заголовок --}}
                        @include('layouts.title', [
                            'name' => 'service',
                        ])
                        {{-- Заголовок --}}

                        {{-- Хлебные крошки --}}
                        @include('layouts.breadcrumbs')
                        {{-- Хлебные крошки --}}

                        <div class="row">

                            <div class="col-md-12">
                                <div class="card box-shadow">
                                    <div class="col-12 mt-3 mb-3">

                                        <div class="section-title">

                                            <div class="title-header">
                                                <h5>Сервис-партнёры</h5>
                                                <h3 class="title">
                                                    <strong>
                                                        Диагностика и ремонт оборудования
                                                    </strong>
                                                </h3>
                                            </div>

                                            <div class="title-desc">
                                                Компания ООО «АТРИ-ЭНЕРГО» - это <b>сертифицированный</b> сервис по обслуживанию теплотехнического оборудования. Мы выполняем гарантийный и постгарантийный ремонт и сервисное обслуживание оборудования.
                                            </div>

                                            <h3 class="h2">Мы производим обслуживание следующего оборудования:</h3>

                                        </div>

                                    </div>

                                    <div class="col-md-12">
                                        <div class="row">

                                            @foreach($items as $item)
                                                <div class="col-md-4 mb-35">
                                                    <div class="e-article wow-delay wow fadeInUp  animated animated"
                                                         style="visibility: visible;">
                                                        <div class="wow fadeInUp cn-mb-15 animated animated mb-2"
                                                             data-wow-delay="0.1s"
                                                             style="visibility: visible; animation-delay: 0.1s;">
                                                            <a class="elem-img-hover img-round cn-d-bl"
                                                               href="@if ($item['route'] ?? false) {{ route($item['route']) }} @else {{ url($item['url']) }} @endif">
                                                                <img class="img-round cn-wp-100"
                                                                     src="{{ $item['img'] }}" alt="">
                                                            </a>
                                                        </div>
                                                        <a class="color__jacksons-purple typo-ondem"
                                                           href="@if ($item['route'] ?? false) {{ route($item['url']) }} @else {{ url($item['url']) }} @endif">
                                                            {{ $item['name'] }}
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-2">
                                        <div class="section-title">
                                            <div class="title-header">
                                                <h2 class="title">КАК С НАМИ СВЯЗАТЬСЯ?</h2>
                                            </div>
                                        </div>

                                        <div class="lh-1-4">
                                            Для получение консультатции или для обращение по сервисным заявкам, в том числе на постгарантийный ремонт, вы можете связаться с нами по телефону или электронной почте, указанными на странице
                                            <a href="{{ url('feedback') }}" target="_blank">«Контакты»</a> Также вы можете оставить
                                            <a href="{{ url('service/order') }}" target="_blank">заявку</a> на сервисное обслуживание и ремонт. Наши обученные специалисты свяжуться с вами, уточнят детали и при необходимости выедут на объект. После чего будет принято решение по ремонту и обслуживанию Если у вас возникли вопросы по рекламациям, то вам необходимо оставить соответствующую заявку в специальной
                                            <a href="{{ url('service/complaints') }}" target="_blank">форме</a>.
                                        </div>

                                    </div>

                                    <div class="btn-group {{--btn-group-sm--}} col mt-4">
                                        <a href="{{ url('feedback') }}" class="btn btn-outline-danger">Контакты</a>
                                        <a href="{{ url('service/order') }}" class="btn btn-outline-danger">Заявки на сервис</a>
                                        <a href="{{ url('service/complaints') }}" class="btn btn-outline-danger">Рекламации</a>
                                    </div>

                                    <div class="col-md-12 mt-4">
                                        {{-- Расскажите о нас --}}
                                        @include('layouts.share')
                                        {{-- Расскажите о нас --}}
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>


    <style type="text/css">
        .ash2, .h2 {
            font-size: 17px;
            font-family: Verdana, sans-serif;
            color: #FA381D;
            padding: 0;
            padding-left: 10px;
            vertical-align: middle;
            border-left: 2px solid #FA381D;
            margin-bottom: 15px;
            line-height: 150%;
            margin-top: 10px;
            display: block;

            font-style: normal;
        }

        img.img_left {
            display: block;
            float: right;
            margin-right: 15px;
            margin-bottom: 10px;
            padding: 3px;
            border: 1px solid #FA381D !important;
        }

        ul li.blues {
            margin-bottom: 10px;
            background: url(/usr/tpl/atri/assest/images/more_bg.gif) 0 6px no-repeat;
            padding-left: 15px;
            list-style: none;
        }

        .leftimg {
            float: left; /* Выравнивание по левому краю */
            margin: 10px 25px 10px 0; /* Отступы вокруг картинки */
        }

        .rightimg {
            float: right; /* Выравнивание по правому краю  */
            margin: 7px 0 7px 7px; /* Отступы вокруг картинки */
        }

    </style>

    <style type="text/css">
        .cmt-list.cmt-list-style-icon li i {
            position: absolute;
            left: auto;
            top: 0px;
            font-weight: 700;
        }

        .ash2, .h2 {
            font-size: 17px;
            font-family: Verdana, sans-serif;
            color: #fa381d;
            padding: 0;
            padding-left: 10px;
            vertical-align: middle;
            border-left: 2px solid #fa381d;
            margin-bottom: 15px;
            line-height: 150%;
            margin-top: 10px;
            display: block;
            font-style: normal;
        }

        a:hover {
            text-decoration: none !important;
        }

        .input-text {
            height: 40px;
            border: 1px solid #ddd;
            font-size: 13px;
            color: #555;
            padding: 0 20px;
            width: 100%;
        }

        label {
            display: inline-block;
            margin: 0;
        }

        textarea:focus, input:focus, select:focus {
            border-color: #ddd !important;
        }

        textarea.input-text {
            height: 105px;
            font-size: 13px;
            line-height: 1.2;
            margin: 5px 0 0;
            padding-top: 15px;
        }

        .toggle {
            margin: 0 0 1.5rem;
            box-sizing: border-box;
            font-size: 0;
            display: flex;
            flex-flow: row nowrap;
            justify-content: flex-end;
            align-items: stretch;
            font-size: 15px;
        }

        .cn-wp-100 {
            width: 100%;
        }

        .img-fit-contain {
            -o-object-fit: contain;
            object-fit: contain
        }

        .img-fit-cover {
            -o-object-fit: cover;
            object-fit: cover
        }

        .img-fit-full {
            -o-object-fit: unset;
            object-fit: unset
        }

        .img-round {
            -webkit-border-radius: 25px;
            -moz-border-radius: 25px;
            border-radius: 25px
        }

        .color__jacksons-purple {
            color: #1e3b87;
        }

        .typo-ondem {
            font-size: 0.88em;
            font-weight: 600;
        }

        .cn-d-bl {
            display: block !important;
        }


        .elem-img-hover {
            position: relative;
            overflow: hidden
        }

        .elem-img-hover:before {
            content: '';
            display: block;
            position: absolute;
            left: 0;
            bottom: 0;
            right: 0;
            top: 0;
            z-index: 1;
            background-image: url(/usr/tpl/main/resources/images/icon/eye.svg);
            -webkit-background-size: 2.50em 2.50em;
            -moz-background-size: 2.50em;
            -o-background-size: 2.50em;
            background-size: 2.50em;
            background-repeat: no-repeat;
            background-position: 50% 50%;
            opacity: 0;
            -webkit-transition: all 240ms;
            -o-transition: all 240ms;
            -moz-transition: all 240ms;
            transition: all 240ms
        }

        .elem-img-hover:after {
            content: '';
            display: block;
            position: absolute;
            background-color: #fa381d;
            -webkit-border-radius: 50px;
            -moz-border-radius: 50px;
            border-radius: 50px;
            width: 6em;
            height: 6em;
            left: 50%;
            top: 50%;
            -webkit-transition: all 400ms;
            -o-transition: all 400ms;
            -moz-transition: all 400ms;
            transition: all 400ms;
            opacity: 0;
            margin-left: -2.5em;
            margin-top: -2.5em
        }

        .elem-img-hover:hover:after {
            opacity: 0.69;
            -webkit-transform: scaleX(4) scaleY(4);
            -moz-transform: scaleX(4) scaleY(4);
            -ms-transform: scaleX(4) scaleY(4);
            -o-transform: scaleX(4) scaleY(4);
            transform: scaleX(4) scaleY(4)
        }

        .elem-img-hover:hover:before {
            opacity: 1
        }

        .elem-img-hover:hover {
            -webkit-transform: scaleX(1.06) scaleY(1.05);
            -moz-transform: scaleX(1.06) scaleY(1.05);
            -ms-transform: scaleX(1.06) scaleY(1.05);
            -o-transform: scaleX(1.06) scaleY(1.05);
            transform: scaleX(1.06) scaleY(1.05)
        }

    </style>

@endsection