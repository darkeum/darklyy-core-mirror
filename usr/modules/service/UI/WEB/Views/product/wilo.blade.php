@extends('app')

@section('content')
    <div id="wrapper">
        <div class="container">
            <div class="row">

                <div id="shopify-section-blog-sidebar"
                     class="shopify-section blog-sidebar col-sidebar sidebar"
                >
                    @include('layouts.sidebar', [
                        'categories' => $categories,
                        'url' => 'service',
                        'all' => true
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

                               <div class="col-md-12">
                                   <h3 class="h2 text-center">
                                       {{ $title ?? '' }}
                                   </h3>

                                   <div class="text-center">
                                       <img src="{{ asset('assets/images/service/wilo_f.jpg') }}"
                                            alt="{{ $title ?? '' }}"
                                            class="w-100 br-3"
                                       >
                                   </div>

                                   <div class="lh-1-3 mt-3">

                                       <div class="">
                                           Неоспоримым преимуществом нашей компании является наличие авторизованного сервисного
                                           центра, обеспечивающего гарантийную, послегарантийную поддержку и сервисное обслуживание
                                           всего спектра продукции
                                           <b>&laquo;WILO&raquo;</b>.Специалисты компании регулярно проходят курсы повышения
                                           квалификации, участвуют в обучающих семинарах, что гарантирует клиентам сервисного
                                           центра высококвалифицированное обслуживание и оказание качественных услуг.
                                           <br><br>
                                           Клиенты нашей компании могут быть спокойны, ведь в случае возникновения вопросов и
                                           нештатных ситуаций есть поддержка Сервиса
                                           <b>&laquo;WILO&raquo;</b>.
                                       </div>

                                       Услуги сервиса включают в себя:

                                       <ul class="blues my-4">
                                           <li class="blues">
                                               консультативную техническую поддержку по телефону и электронной почте;
                                           </li>
                                           <li class="blues">контроль монтажа <b>оборудования &laquo;WILO&raquo;</b>;</li>
                                           <li class="blues">ввод в эксплуатацию;</li>
                                           <li class="blues">диагностика;</li>
                                           <li class="blues">техническое обслуживание;</li>
                                           <li class="blues">ремонт.</li>
                                       </ul>

                                       Стоимость работ вы может посмотреть в <a href="/content/service_price.html">прайс-листе</a> сервисного обслуживания насосного оборудования WILO.

                                   </div>

                                   {{-- Сертификаты --}}
{{--                                   @include('service::layouts.certs')--}}
                                   {{-- Сертификаты --}}

                                   {{-- Кнопки действия --}}
                                   @include('service::layouts.action_button', [
                                       'url' => 'order/service/wilo',
                                   ])
                                   {{-- Кнопки действия --}}

                               </div>

                           </div>
                       </div>

                    </div>

                </div>
            </div>
        </div>
@endsection