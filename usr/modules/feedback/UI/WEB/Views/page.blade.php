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
                            'url' => 'feedback'
                        ])
                    </div>

                    <div id="shopify-section-blog-template" class="shopify-section">

                        {{-- Заголовок --}}
                        @include('layouts.title', [
                            'name' => 'feedback',
                        ])
                        {{-- Заголовок --}}

                        {{-- Хлебные крошки --}}
                        @include('layouts.breadcrumbs')
                        {{-- Хлебные крошки --}}

                        <div class="row">

                            <div class="col-md-12">
                                <div class="card p-2 pb-3 box-shadow">

                                    <div class="col-12 mt-3 mb-3">

                                        <div class="section-title">
                                            <div class="title-header">
                                                <h5>Наши контакты</h5>
                                                <h2 class="title">
                                                    <strong>
                                                        Как с нами связаться?
                                                    </strong>
                                                </h2>
                                            </div>
                                            <div class="title-desc">
                                                Свяжитесь с нами по телефону, задайте вопрос специалисту по почте или
                                                решите возникшие сложности с помощью этого раздела
                                            </div>
                                            <p style="line-height: 1.3;">
                                                Наша компания специализируется на установке
                                                систем водоснабжения, систем отопления. <br> ООО «АТРИ-ЭНЕРГО» -
                                                динамично развивающаяся
                                                многопрофильная компания, направлением деятельности которой является
                                                внедрение и применение
                                                современных технологий для решения инженерных задач в области систем
                                                тепловодоснабжения.
                                            </p>
                                        </div>

                                        <div class="contact-us">
                                            <ul class="contact-list">
                                                <li>
                                                    <i style="width: 10px;" class="fal fa-map-marker-alt fa-lg mr-3"></i>
                                                    <strong>Адрес: </strong>
                                                    <a href="#maps">
                                                        {{ config('theme.req.address') }}
                                                    </a>
                                                </li>

                                                <li>
                                                    <i style="width: 10px;" class="fal fa-envelope fa-lg mr-3"></i>
                                                    <strong>E-mail: </strong>
                                                    <a href="mailto:{{ config('theme.req.email') }}">
                                                        {{ config('theme.req.email') }}
                                                    </a>
                                                </li>

                                                <li>
                                                    <i style="width: 10px;" class="fal fa-phone-rotary fa-lg mr-3"></i>
                                                    <strong>Телефон: </strong>
                                                    <a href="tel:{{ config('theme.req.tel') }}">
                                                        {{ config('theme.req.tel') }}
                                                    </a>
                                                </li>

                                                <li>
                                                    <i style="width: 10px;" class="fal fa-file-alt fa-lg mr-3"></i>
                                                    <strong>Реквизиты: </strong>
                                                    <a href="{{ url('content/documents.html') }}">
                                                        Перейти в раздел документов
                                                    </a>
                                                </li>

                                                <li>
                                                    <i style="width: 10px;" class="fal fa-comment-alt-dots fa-lg mr-3"></i>
                                                    <strong>Обратна связь: </strong>
                                                    <a href="#feedback">
                                                        Написать
                                                    </a>
                                                </li>

                                            </ul>
                                        </div>

                                        <p style="line-height: 1.3;">Не забудьте подписаться на нас в социальных сетях.
                                            Там мы публикуем самые
                                            свежие новости, а также различные акции. Кроме того в социальных сетях мы
                                            переодически проводим
                                            розыгрыши с призами.</p>

                                        <ul class="social-icons circle social-hover cmt-textcolor-skincolor mt-3">
                                            <li>
                                                <a class="tooltip-top"
                                                   target="_blank"
                                                   href="https://www.instagram.com/atrienergo/"
                                                   data-tooltip="Instagram">
                                                    <i class="fab fa-instagram" aria-hidden="true"></i>
                                                </a>
                                            </li>

                                            <li>
                                                <a class="tooltip-top"
                                                   target="_blank"
                                                   href="https://vk.com/atrienergo"
                                                   data-tooltip="Вконтакте">
                                                    <i class="fab fa-vk" aria-hidden="true"></i>
                                                </a>
                                            </li>

                                            <li>
                                                <a class="tooltip-top"
                                                   target="_blank"
                                                   href="https://www.facebook.com/atrienergo"
                                                   data-tooltip="Facebook">
                                                    <i class="fab fa-facebook" aria-hidden="true"></i>
                                                </a>
                                            </li>

                                            <li>
                                                <a class="tooltip-top" target="_blank"
                                                   href="https://www.youtube.com/channel/UCkO2BsLpVESgkp8YQnT-pTw"
                                                   data-tooltip="YouTube">
                                                    <i class="fab fa-youtube" aria-hidden="true"></i>
                                                </a>
                                            </li>

                                        </ul>
                                    </div>

                                    {{-- Карта --}}
                                    {{--@include('blocks.map', ['type' => 'json'])--}}
                                    @include('feedback::layouts.map')
                                    {{-- Карта --}}

                                    {{-- Форма - Обратная связь --}}
                                    @include('feedback::layouts.form')
                                    {{-- Форма - Обратная связь --}}

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
    </style>

@endsection