@extends('job::page')

@section('page-content')

    <style>
        a:hover {
            text-decoration: none !important;
        }

        .cmt-list.cmt-list-style-icon li i {
            position: absolute;
            left: auto;
            top: 2px;
            font-weight: 700;
        }

    </style>
    <div class="row">
        <div class="col-12 mt-3">
            <div class="section-title">
                <div class="title-header">
                    <h5>Добро пожаловать в {{ $item->categories['name'] }} АТРИ-ЭНЕРГО</h5>
                    <h2 class="title">О направлении</h2>
                </div>
            </div>
            <p style="line-height: 1.2;">{{ $item->categories['description'] }} Посмотрите другие вакансии по данному
                направлению.</p>
        </div>
    </div>
    <div class="cmt-bgcolor-white box-shadow pt-10 pl-30 pb-40 pr-30 mt-3 mb-3">
        <div class="row">
            <div class="pt-15">
                <div class="right-content">
                    <div class="col-sm-12">
                        <div class="row  align-items-center">
                            <div class="col-sm-6">
                                <div class="section-title">
                                    <div class="title-header">
                                        <h5>Присоединяйся нашей команде</h5>
                                        <h2 class="title">О вакансии</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 text-right">
                                <a href="/job/send"
                                   class="cmt-btn cmt-btn-size-md cmt-btn-shape-round cmt-btn-style-border cmt-btn-color-skincolor">Отправить
                                    резюме</a>
                            </div>
                            <div class="col-sm-12">
                                <p style="line-height: 1.2;">{{ $item['description'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <!-- featured-imagebox -->
                            <div class="featured-icon-box icon-align-top-content style8 text-left box-shadow">
                                <div class="featured-icon">
                                    <div class="cmt-icon cmt-icon_element-onlytxt cmt-icon_element-color-skincolor cmt-icon_element-size-md">
                                        <i class="fad fa-users"></i>
                                    </div>
                                </div>
                                <div class="featured-content">
                                    <div class="content-inner">
                                        <div class="featured-title">
                                            <h5>Дружный коллектив</h5>
                                            <p>Каждый сотрудник - участник команды, которая ежедневно работает над
                                                развитием себя и компании.</p>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- featured-imagebox end-->
                        </div>
                        <div class="col-sm-4">
                            <!-- featured-imagebox -->
                            <div class="featured-icon-box icon-align-top-content style8 text-left box-shadow">
                                <div class="featured-icon">
                                    <div class="cmt-icon cmt-icon_element-onlytxt cmt-icon_element-color-skincolor cmt-icon_element-size-md ">
                                        <i class="fad fa-shield-check"></i>
                                    </div>
                                </div>
                                <div class="featured-content">
                                    <div class="content-inner">
                                        <div class="featured-title">
                                            <h5>Под защитой</h5>
                                            <p>Мы работаем по ТКРФ, кроме того компания всегда заботится о своих
                                                сотрудниках и помогает им в сложных ситуациях</p>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- featured-imagebox end-->
                        </div>
                        <div class="col-sm-4">
                            <!-- featured-imagebox -->
                            <div class="featured-icon-box icon-align-top-content style8 text-left box-shadow">
                                <div class="featured-icon">
                                    <div class="cmt-icon cmt-icon_element-onlytxt cmt-icon_element-color-skincolor cmt-icon_element-size-md ">
                                        <i class="fad fa-gift"></i>
                                    </div>
                                </div>
                                <div class="featured-content">
                                    <div class="content-inner">
                                        <div class="featured-title">
                                            <h5>Приятные бонусы</h5>
                                            <p>У нас есть различные бонусы для успешных сотрудников. Наши сотрудники
                                                посещают выставки и семинары.</p>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- featured-imagebox end-->
                        </div>
                    </div>
                </div>
                <div class="pt-15">
                    <h4>Условия</h4>
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <ul class="cmt-list cmt-list-style-icon cmt-list-icon-color-skincolor mb-10">
                                @foreach($item['conditions'] as $condition)
                                    <li>
                                        <i class="text-success fad fa-check-circle"></i>
                                        <span class="cmt-list-li-content">{{ $condition }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="pt-15">
                    <h4>Обязанности</h4>
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <ul class="cmt-list cmt-list-style-icon cmt-list-icon-color-skincolor mb-10">
                                @foreach($item['duties'] as $duti)
                                    <li>
                                        <i class="text-success fad fa-check-circle"></i>
                                        <span class="cmt-list-li-content">{{ $duti }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="pt-15">
                    <h4>Требования</h4>
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <ul class="cmt-list cmt-list-style-icon cmt-list-icon-color-skincolor mb-10">
                                @foreach($item['requirements'] as $requirement)
                                    <li>
                                        <i class="text-success fad fa-check-circle"></i>
                                        <span class="cmt-list-li-content">{{ $requirement }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="pt-40">
                    <div class="section-title">
                        <div class="title-header">
                            <h5>Давай знакомится</h5>
                            <h2 class="title">Руководитель направления</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <p>
                                Получить консультацию вы можете у руководителя данного направления:
                                @if($responsible ?? false)
                                    <b>{{ $responsible['surname'] }} {{ $responsible['name'] }}</b>
                                    <a href="tel:{{ $responsible['phone'] }}">
                                        {{ $responsible['phone'] }}</a>.
                                    Также вы можете отправить свой вопрос или предложение на электронную почту
                                    <a href="mailto:{{ $responsible['email'] }}">{{ $responsible['email'] }}</a>,
                                @endif
                                <br>
                                кроме того вы можете воспользоваться формой обратной связи в разделе
                                <a href="{{ url('feedback') }}">«Контакты»</a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="pt-40">
                    <div class="section-title">
                        <div class="title-header">
                            <h5>Ижевск, ул. Салютовская, 5А, 3 этаж</h5>
                            <h2 class="title">Наш офис</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <script type="text/javascript" charset="utf-8" async
                                    src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A2282d7c33b0537643e7c6e269b52c13d64d56971b3285efe36363dc704d113bd&amp;width=100%25&amp;height=305&amp;lang=ru&amp;scroll=false"></script>
                        </div>
                    </div>
                </div>
                <div class="pt-40">
                    <div class="section-title">
                        <div class="title-header">
                            <h5>Расскажи о нас своим друзьям</h5>
                            <h2 class="title">Поделиться</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <script src="https://yastatic.net/share2/share.js"></script>
                            <div class="ya-share2" data-curtain data-size="l" data-shape="round" data-limit="7"
                                 data-services="vkontakte,facebook,odnoklassniki,messenger,telegram,twitter,viber,whatsapp,moimir,skype,tumblr,evernote,linkedin"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('job::layouts.form')

    <div class="row">
        @include('job::layouts.faq')
    </div>

@endsection