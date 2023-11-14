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
                                <div class="card p-2 box-shadow">
                                    <div class="col-12 mt-3 mb-3">
                                        <div class="section-title">
                                            <div class="title-header">
                                                <h5>Наши контакты</h5>
                                                <h2 class="title"><strong>Как с нами связаться?</strong></h2>
                                            </div>
                                            <div class="title-desc">Свяжитесь с нами по телефону, задайте вопрос специалисту по почте или решите
                                                возникшие сложности с помощью этого раздела
                                            </div>
                                            <p style="line-height: 1.3;">Наша компания специализируется на установке систем водоснабжения,
                                                систем отопления. <br> ООО «АТРИ-ЭНЕРГО» - динамично развивающаяся многопрофильная компания,
                                                направлением деятельности которой является внедрение и применение современных технологий для
                                                решения инженерных задач в области систем тепловодоснабжения.</p>
                                        </div>

                                        <form class="form-horizontal" method="POST"
                                              action="{{ route('service.order.send') }}"
                                        >

                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Тип оборудования</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" name="cat"
                                                            {{--onchange="change_select(this)"--}}
                                                    >

                                                        @foreach($cats as $cat)
                                                            <option value="{{ $cat['id'] }}"> {{ $cat['name'] }} </option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Название заказчика/плательщика</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="org"
                                                           placeholder="Введите название заказчика/плательщика">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">
                                                    Наименование объекта, где установлено оборудование
                                                </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="object"
                                                           placeholder="Введите наименование объекта">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Адрес заказчика/объекта</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="address"
                                                           placeholder="Укажите Адрес заказчика/объекта">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Контактное лицо</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="name" placeholder="Укажите контактное лицо">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">E-mail</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="email" placeholder="Укажите ваш E-mail">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Телефон</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="phone"
                                                           placeholder="Укажите телефон для связи">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Краткое описание неисправности</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="message" rows="5" placeholder="Краткое описание неисправности"></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row" id="div1">
                                                <label class="col-sm-3 col-form-label">Артикул оборудования</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="sku"
                                                           placeholder="Введите артикул оборудования">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Тип оборудования</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="type" placeholder="Укажите тип оборудования">
                                                </div>
                                            </div>

                                            <div class="form-group row" id="div2">
                                                <label class="col-sm-3 col-form-label">Серийный номер</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="serial" placeholder="Введите серийный номер">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Дата производства </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="date" placeholder="Укажите дату производства">
                                                </div>
                                            </div>

                                            <div class="form-group row" id="div3">
                                                <label class="col-sm-3 col-form-label">Номер гарантийного талона </label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="numb"
                                                           placeholder="Введите № гарантийного талона ">
                                                </div>
                                            </div>

                                        {{--<div class="form-group row">--}}
                                        {{--    <label class="col-sm-3 col-form-label p-0"></label>--}}

                                        {{--    <div class="col-sm-8">--}}
                                        {{--        --}}
                                        {{--    </div>--}}
                                        {{--</div>--}}

                                        <!-- Button  -->
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label"></label>

                                                <div class="col-sm-8">
                                                    <button type="submit" class="btn btn-success btn-lg">Отправить</button>
                                                </div>

                                            </div>

                                        </form>

                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- top twig --}}
    <style type="text/css">
        @media (min-width: 768px) {
            .template-article .col-sidebar,
            .template-blog .col-sidebar {
                display: block !important;
            }
        }

        @media (max-width: 767px) {
            .template-article .col-sidebar,
            .template-blog .col-sidebar {
                display: none;
            }

            .template-article #displayTextLeft,
            .template-blog #displayTextLeft {
                display: block;
            }
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function () {
            $('select[name="cat"]').change(function () {
                var el = $(this).val();
                if (el == "wilo") {
                    $('#div1').css('display', 'block');
                    $('#div2').css('display', 'block')
                    $('#div3').css('display', 'block')
                    $('#div4').css('display', 'block')

                } else {
                    $('#div1').css('display', 'none');
                    $('#div2').css('display', 'none');
                    $('#div3').css('display', 'none');
                    $('#div4').css('display', 'none');
                }
            });
        }); /*end ready*/
    </script>
    {{-- top twig --}}

    {{-- bottom twig --}}
    <script src="https://www.google.com/recaptcha/api.js?render=6LfddtUZAAAAAELCRiYyqMJSTIeYCNHvU7E5_xbP"></script>
    <script type="application/javascript">
        $('#feedbackSend').on("submit", function (e) {
            const form = this;
            e.preventDefault();
            $("#btnFetch").prop("disabled", true);
            $("#btnFetch").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Отправка...');
            grecaptcha.ready(function () {
                grecaptcha.execute('6LfddtUZAAAAAELCRiYyqMJSTIeYCNHvU7E5_xbP', {action: 'contact'}).then(async function (token) {
                    var recaptchaResponse = document.getElementById('recaptchaResponse');
                    recaptchaResponse.value = token;
                    try {
                        const data = new FormData(form);
                        const _response = await (await fetch(`/feedback/ajax/send`, {
                            method: "POST",
                            body: data
                        })).json();
                        var type = _response.type;
                        var error = _response.error;
                        var errorMessage = '';
                        var success = _response.success;
                        if (type != "success") {
                            for (var k in error) {
                                errorMessage = errorMessage + error[k] + '<br>';
                                console.log(errorMessage);
                            }
                            Swal.fire('Ошибка', errorMessage, 'error');
                            $("#btnFetch").prop("disabled", false);
                            $("#btnFetch").html('Отправить');
                        } else {
                            Swal.fire('Информация', success, 'success');
                            $('#btnFetch').remove();
                            $('#feedbackSend').html('<div class="alert alert-success alert-lg w-100 mt-3"><h4 class="no-margin no-padding">Информация</h4><p>' + success + '</p></div>');
                        }
                    } catch (e) {
                        const error = e.message;
                        $("#btnFetch").prop("disabled", false);
                        $("#btnFetch").html('Отправить');
                        Swal.fire('Ошибка', error, 'error');
                    }
                });
            });

        });
    </script>
    {{-- bottom twig --}}

@endsection