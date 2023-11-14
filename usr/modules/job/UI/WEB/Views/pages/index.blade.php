@extends('job::page')

@section('page-content')
    <div class="row">
        <div class="col-12 mt-3">

            <div class="section-title">
                <div class="title-header">
                    <h5>Добро пожаловать в АТРИ-ЭНЕРГО</h5>
                    <h2 class="title">Присоединяйся <strong>к нашей команде</strong></h2>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="pt-15">

                        <p style="line-height: 1.5;">Наша компания специализируется на установке
                            систем водоснабжения, систем отопления <br> ООО «Атри-Энерго» -
                            динамично развивающаяся многопрофильная компания, направлением
                            деятельности которой является внедрение и применение современных
                            технологий для решения инженерных задач в области систем
                            тепловодоснабжения.</p>

                        <div class="section-title">
                            <div class="title-header">
                                <h4 class="title text-uppercase">Почему именно <strong>мы?</strong>
                                </h4>
                            </div>
                        </div>

                        <div class="pt-10 pr-15 res-991-pr-0">
                            <div class="featured-icon-box icon-align-before-content style2">

                                <div class="featured-icon">
                                    <div class="cmt-icon cmt-icon_element-border cmt-icon_element-color-skincolor cmt-icon_element-size-lg cmt-icon_element-style-square">
                                        <i class="fad fa-users"></i>
                                    </div>
                                </div>

                                <div class="featured-content">
                                    <div class="featured-title">
                                        <h5 class="text-red">Дружный коллектив</h5>
                                    </div>
                                    <div class="featured-desc">
                                        <p>Большую часть своей жизни мы проводим на работе, поэтому
                                            самое важное для нас - дружный коллектив. Каждый
                                            сотрудник - настоящий участник команды, которая
                                            ежедневно работает над развитием себя и компании.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="featured-icon-box icon-align-before-content style2">
                                <div class="featured-icon">
                                    <div class="cmt-icon cmt-icon_element-border cmt-icon_element-color-skincolor cmt-icon_element-size-lg cmt-icon_element-style-square">
                                        <i class="fad fa-shield-check"></i>
                                    </div>
                                </div>

                                <div class="featured-content">
                                    <div class="featured-title">
                                        <h5 class="text-red">Под защитой</h5>
                                    </div>
                                    <div class="featured-desc">
                                        <p>Мы работаем по трудовому кодексу, кроме того компания
                                            всегда заботится о своих сотрудниках и помогает им в
                                            сложных ситуациях, кроме того компания на рынке уже 9
                                            лет, что явлется показателем стабильности и
                                            надежности.</p>
                                    </div>
                                </div>

                            </div>

                            <div class="featured-icon-box icon-align-before-content style2">
                                <div class="featured-icon">
                                    <div class="cmt-icon cmt-icon_element-border cmt-icon_element-color-skincolor cmt-icon_element-size-lg cmt-icon_element-style-square">
                                        <i class="fad fa-gift"></i>
                                    </div>
                                </div>
                                <div class="featured-content">
                                    <div class="featured-title">
                                        <h5 class="text-red">Приятные бонусы</h5>
                                    </div>
                                    <div class="featured-desc">
                                        <p>У нас есть различные поощрения и бонусы для успешных
                                            сотрудников. Наши сотрудники постоянно посещают выставки
                                            и семинары. Мы оплачиваем различные курсы и тренинги для
                                            повышения квалификации. Также мы проводим различные
                                            мероприятия к корпоративы.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 mt-3">
            <div class="section-title" style="margin-bottom: -5px;">
                <div class="title-header">
                    <h5>Смотри, какие есть направления</h5>
                    <h2 class="title">Вакансии по направлениям</h2>
                </div>
            </div>
        </div>

        {{-- Категории --}}
        @include('job::layouts.categories', collect('categories'))
        {{-- Категории --}}

        {{-- ДАВАЙ РАБОТАТЬ ВМЕСТЕ --}}
        <div class="col-lg-12 col-md-12 col-sm-12 mt-3">

            <div class="section-title" style="margin-bottom: -5px;">
                <div class="title-header">
                    <h5>Давай работать вместе</h5>
                    <h2 class="title">Не нашли подходящей вакансии?</h2>
                </div>
            </div>

            <div class="row mt-3 mb-3">

                <div class="col-sm-4 m-t-xs">
                    <span style="line-height: 1.2;" class="text-large m-b-0 ">
                        Напишите несколько слов о себе и о том, какая вакансия может быть вам интересна.
                    </span>
                    <a href="{{ url('job/send') }}"
                       class="cmt-btn cmt-btn-size-md cmt-btn-shape-round cmt-btn-style-border cmt-btn-color-skincolor d-block position-relative text-center mt-3">
                        Отправить резюме
                    </a>
                </div>

                <div class="col-sm-4 m-t-xs">
                    <span style="line-height: 1.2;" class="text-large m-b-0">
                        Подпишитесь и получайте информацию о новых вакансиях на свою почту.
                    </span>
                    <a href="{{ url('job/subscribe') }}"
                       class="cmt-btn cmt-btn-size-md cmt-btn-shape-round cmt-btn-style-border cmt-btn-color-skincolor d-block position-relative text-center mt-3">
                        Подписаться
                    </a>
                </div>
                <div class="col-sm-4 m-t-xs">
                    <span style="line-height: 1.2;" class="text-large">
                        Задайте вопрос менеджеру по телефону @lang('app.phone_num_text') или напишите письмо.
                    </span>
                    <a href="{{ url('feedback?type=3') }}"
                       class="cmt-btn cmt-btn-size-md cmt-btn-shape-round cmt-btn-style-border cmt-btn-color-skincolor d-block position-relative text-center mt-3">
                        Написать письмо
                    </a>
                </div>
            </div>
        </div>
        {{-- ДАВАЙ РАБОТАТЬ ВМЕСТЕ --}}

        {{-- faq --}}
        @include('job::layouts.faq')
        {{-- faq --}}

    </div>
@endsection