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
                                        <img src="{{ asset('assets/images/service/ridan_f.jpg') }}"
                                             alt="{{ $title ?? '' }}"
                                             class="w-100 br-3"
                                        >
                                    </div>

                                    <div class="lh-1-3 mt-3">
                                        <div class="mt-3">
                                            Правильность эксплуатации теплообменного оборудования и своевременное
                                            квалифицированное обслуживание обеспечивают эффективную работу всего оборудования.
                                            ООО <b>«АТРИ-ЭНЕРГО»</b> предоставляет Заказчикам гарантийное и послегарантийное
                                            сервисное обслуживание
                                            на всё теплообменное оборудование, поставляемое компанией Ридан, через сеть
                                            официальных партнёров.
                                        </div>

                                        <h5 class="mt-3">Являясь официальным сервисным партнёром наша организация имеет право и
                                            полномочия от компании «Ридан» проводить широкий спектр работ, связанных с
                                            сервисным обслуживанием оборудования, таких как:</h5>

                                        <div class="mt-3">

                                            <p class="d-flex align-items-center">
                                                <img src="{{ asset('assets/images/service/i1.png') }}"
                                                     class="float-left mr-3"
                                                     alt="Оказание инжиниринговых услуг" width="48"/>
                                                Оказание инжиниринговых услуг (выявление, анализ причин и предоставление рекомендаций по устранению недостатков работы оборудования, поставленного компанией «Ридан»);
                                            </p>

                                            <p class="d-flex align-items-center">
                                                <img src="{{ asset('assets/images/service/i2.png') }}" class="float-left mr-3"
                                                     alt="Оказание инжиниринговых услуг" width="48"/>
                                                Осуществление консультирования по техническим вопросам, связанным с эксплуатацией
                                                теплообменного оборудования;
                                            </p>

                                            <p class="d-flex align-items-center">
                                                <img src="{{ asset('assets/images/service/i3.png') }}" class="float-left mr-3"
                                                     alt="Оказание инжиниринговых услуг" width="48"/>
                                                Техническое обслуживание оборудования, поставленного компанией «Ридан», по гарантии,
                                                включая замену комплектующих и изделий целиком;
                                            </p>

                                            <p class="d-flex align-items-center">
                                                <img src="{{ asset('assets/images/service/i4.png') }}" class="float-left mr-3"
                                                     alt="Оказание инжиниринговых услуг" width="48"/>
                                                Выполнение пуско-наладочных работ, ремонтных работ и иных видов работ, направленных
                                                на восстановление/поддержание работоспособности и эксплуатационных характеристик
                                                оборудования, а также поставка необходимых комплектующих и запасных частей для
                                                оборудования.
                                            </p>

                                        </div>

                                        <h5 class="mt-xl-4">Преимущества работы с Сервис партнером «Ридан»</h5>
                                        <ul class="blues mt-3">
                                            <li class="blues">Оперативность работы – срок реагирования на сервисные запросы
                                                составляет 24 часа.
                                            </li>
                                            <li class="blues">Индивидуальный подход к каждому Заказчику.</li>
                                            <li class="blues">Высокий профессионализм специалистов, прошедших обучение и
                                                аттестацию в компании «Ридан»
                                            </li>
                                            <li class="blues">Уверенность в качестве - поставка оригинальных запасных
                                                частей
                                            </li>
                                        </ul>
                                    </div>

                                    {{-- Сертификаты --}}
{{--                                    @include('service::layouts.certs')--}}
                                    {{-- Сертификаты --}}

                                    {{-- Кнопки действия --}}
                                    @include('service::layouts.action_button', [
                                        'url' => 'order/service/ridan',
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