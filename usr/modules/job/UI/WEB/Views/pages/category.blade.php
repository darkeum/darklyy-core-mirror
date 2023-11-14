@extends('job::page')

@section('page-content')
    <style>
        .h2 {
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

        .ah2:hover {
            text-decoration: none;
        }

        img.img_left {
            display: block;
            float: right;
            margin-right: 15px;
            margin-bottom: 10px;
            padding: 3px;
            border: 1px solid #E2E3E5 !important;
            max-width: 214px !important;
        }

        .cmt-badge {
            display: inline-block;
            vertical-align: middle;
            font-size: 16px;
            font-family: 'Roboto', sans-serif;
            line-height: normal;
            background: transparent;
            border-width: 1px;
            border-style: solid;
            border-color: #eaeff5;
            position: relative;
            font-weight: 500;
            overflow: hidden;
            z-index: 1;
            transition: all 0.5s ease 0s;
            border-radius: 2em;
            font-size: 15px;
            padding: 5px 20px 5px 20px;
            border-color: rgb(44, 184, 137);
            color: rgb(44, 184, 137);
        }
    </style>
    <div class="row">
        <div class="col-12 mt-3">
            <div class="section-title">
                <div class="title-header">
                    <h5>Добро пожаловать в {{ $category['name'] }} АТРИ-ЭНЕРГО</h5>
                    <h2 class="title">Выберите <strong>должность</strong></h2>
                </div>
            </div>
            <p style="line-height: 1.5;">{{ $category['description'] }}</p>

            @if($items->isNotEmpty())
                <div class="card">
                    <div class="card-body card-body-cert">
                        <div class="pb-20 mb_30">
                            @foreach($items as $item)
                                <div class="row align-items-center">
                                    <div class="col-lg-6">
                                        <a href="{{ $item['url'] }}" class="ah2"><h3
                                                    class="h2">{{ $item['title'] }}</h3></a>
                                    </div>
                                    <div class="col-lg-6 text-right">
                                <span class="cmt-badge">
                                    @if($item['salary'] ?? false){{ 'По договоренности' }}@else{{ $item['salary'] }}@endif
                                </span>
                                    </div>
                                </div>
                                <p>{{ $item['description'] }}</p>
                                <a href="{{ $item['url'] }}"
                                   class="cmt-btn cmt-btn-size-sm cmt-btn-shape cmt-btn-style-fill cmt-btn-color-skincolor mb-3">Подробнее</a>
                            @endforeach

                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning alert-lg w-100 mt-3" role="alert">
                    <h4 class="no-margin no-padding">Информация</h4>
                    <p>К сожалению по данному запросу вакансий не найдено, попробуйте зайти позже!</p>
                </div>
            @endif
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 mt-3">
            <div class="section-title" style="margin-bottom: -5px;">
                <div class="title-header">
                    <h5>Давай работать вместе</h5>
                    <h2 class="title">Не нашли подходящей вакансии?</h2>
                </div>
            </div>
            <div class="row mt-3 mb-3">
                <div class="col-sm-4 m-t-xs">
                    <p style="line-height: 1.2;" class="text-large m-b-0">
                        Напишите несколько слов о себе и о том, какая вакансия может быть вам интересна.
                        <br><br>
                        <a href="/job/send"
                           class="cmt-btn cmt-btn-size-md cmt-btn-shape-round cmt-btn-style-border cmt-btn-color-skincolor">Отправить
                            резюме</a>
                    </p>
                </div>
                <div class="col-sm-4 m-t-xs">
                    <p style="line-height: 1.2;" class="text-large m-b-0">
                        Подпишитесь и получайте информацию о новых вакансиях на свою почту.
                        <br><br>
                        <a href="/job/subscribe"
                           class="cmt-btn cmt-btn-size-md cmt-btn-shape-round cmt-btn-style-border cmt-btn-color-skincolor">Подписаться</a>
                    </p>
                </div>
                <div class="col-sm-4 m-t-xs">
                    <p style="line-height: 1.2;" class="text-large">Задайте вопрос менеджеру по телефону @lang('app.phone_num_text') или напишите письмо.</p>
                    <a href="{{ url('feedback?type=3') }}"
                       class="cmt-btn cmt-btn-size-md cmt-btn-shape-round cmt-btn-style-border cmt-btn-color-skincolor">Написать
                        письмо</a>
                </div>
            </div>
        </div>

        @include('job::layouts.faq')

    </div>
@endsection