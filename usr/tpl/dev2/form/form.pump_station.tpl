<div id="wrapper">
    <div class="main-container">
        <div class="container">
            <div class="row">
                <div id="shopify-section-blog-sidebar" class="shopify-section blog-sidebar col-sidebar">
                    <div class="block widget-recent spaceBlock">
                        <div class="block-content">
                            <ul>
                                <li class="article active"><a href="/content/page/mounting">Монтаж</a></li>
                                <li class="article"><a href="/content/certificates.html">Сертификаты и лицензии</a></li>
                                <li class="article"><a href="/project/installation_objects">Наши объекты</a></li>
                                <li class="article"><a href="/project/map">Карта объектов</a></li>
                            </ul>
                        </div>
                    </div>
                    {%TPL:left.menu%}
                </div>
                <div id="shopify-section-blog-template" class="shopify-section col-main">
                    <div class="blog-post-wrapper">
                        <article class="blog-post-item">
                            <div class="inner-box">
                                <div class="blog-post-content-desc">
                                    <div class="blog-post-content">
                                        <h1>Подбор насосной станции</h1>
                                        <ul id="breadcrumbs-two" class="breadcrumb" >
                                            <li><a href="/" >Главная</a><span class="go-page"></span></li>
                                            <li class="active" ><span>Подбор насосной станции</span></li>
                                        </ul>
                                        <div class="nasos-article-text">



                                            <form class="form-horizontal" method="POST" action="/form/send/pump_station">


                                                <div class="form-group row">
                                                    <label for="" class="col-sm-3 col-form-label"><strong>Система установки насосной станции</strong></label>
                                                    <div class="col-sm-8">
                                                        <div class="form-check form-check-inline">
                                                            <label class="form-check-label">
                                                                <input class="form-check-input" name="r1" value="1" type="radio"> Повысительный хвс/гвс
                                                            </label>
                                                        </div><br>
                                                        <div class="form-check form-check-inline">
                                                            <label class="form-check-label">
                                                                <input class="form-check-input" name="r1" value="2" type="radio"> Станция пожаротушения
                                                            </label>
                                                        </div><br>
                                                        <div class="form-check form-check-inline">
                                                            <label class="form-check-label">
                                                                <input class="form-check-input" name="r1" value="3" type="radio"> Пропиточная система отопления
                                                            </label>
                                                        </div><br>
                                                        <div class="form-check form-check-inline">
                                                            <label class="form-check-label">
                                                                <input class="form-check-input" name="r1" value="4" type="radio"> Другое назначение

                                                            </label>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="form-group row pd-nas-rashod">
                                                    <label  class="col-sm-3 col-form-label">Расход</label>
                                                    <div class="col-sm-4">
                                                        <div class="input-group">

                                                            <input type="text" class="form-control" name="rashod" aria-label="Price">
                                                            <span class="input-group-addon">м3/ч</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row pd-nas-napor">
                                                    <label  class="col-sm-3 col-form-label">Напор</label>
                                                    <div class="col-sm-4">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" name="napor"  aria-label="Price">
                                                            <span class="input-group-addon">м.в.ст.</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="nasos-div-img-grid">
                                                        <div class="nasos-div-img-grid-left">
                                                            <div class="form-group row">
                                                                <label  class="col-sm-3 col-form-label">Количество насосов в станции (включая резервные)</label>
                                                                <div class="col-sm-4">
                                                                    <div class="input-group">
                                                                        <select class="form-control" name="sum" onchange="kolVoNasosStan(this); kolVoNasosStanSel(this)">
                                                                            <option>2</option>
                                                                            <option>3</option>
                                                                            <option>4</option>
                                                                            <option>5</option>
                                                                            <option>6</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label  class="col-sm-3 col-form-label">Количество резервных насосов</label>
                                                                <div class="col-sm-4">
                                                                    <div class="input-group">
                                                                        <select class="form-control" name="sumr" onchange="kolVoNasosStan(this)">
                                                                            <option>0</option>
                                                                            <option>1</option>
                                                                            <option>2</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label  class="col-sm-3 col-form-label">Рабочая точка на сколько насосов работающих одновременно?</label>
                                                                <div class="col-sm-4">
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control" name="tocha" aria-label="Price">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="nasos-div-img">
                                                            <div class="nasos-div-img-dv">
                                                                <div class="nasos-div-img1"></div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="form-group row">
                                                    <label for="" class="col-sm-3 col-form-label"><strong>Применение встроенного частотного преобразавтеля</strong></label>
                                                    <div class="col-sm-8">
                                                        <div class="form-check form-check-inline">
                                                            <label class="form-check-label">
                                                                <input class="form-check-input" name="r2" value="1" type="radio"> Насосы со строенным ПЧ


                                                            </label>
                                                        </div><br>
                                                        <div class="form-check form-check-inline">
                                                            <label class="form-check-label">
                                                                <input class="form-check-input" name="r2" value="2" type="radio"> Шкаф управления с ПЧ


                                                            </label>
                                                        </div><br>
                                                        <div class="form-check form-check-inline">
                                                            <label class="form-check-label">
                                                                <input class="form-check-input" name="r2" value="3" type="radio"> Шкаф управления "Прямой пуск"
                                                            </label>
                                                        </div>


                                                    </div>

                                                </div>






                                                <div class="teplo-grid-bt">
                                                    <div class="block_cont">
                                                        <div class="block_name block_name_h1 block_name_str_h1">Контактные данные</div>
                                                        <div class="block_cont_over">
                                                            <div class="block_cont">
                                                                <div class="block_cont_name">Организация</div>
                                                                <input type="text" class="form-control" name="org" placeholder="Введите организацию">
                                                            </div>
                                                            <div class="block_cont">
                                                                <div class="block_cont_name">ИНН организации</div>
                                                                <input type="text" class="form-control" name="inn" placeholder="Введите ИНН организации">
                                                            </div>
                                                            <div class="block_cont">
                                                                <div class="block_cont_name">Контактное лицо</div>
                                                                <input type="text" class="form-control" name="name" placeholder="Укажите контактное лицо">
                                                            </div>
                                                            <div class="block_cont">
                                                                <div class="block_cont_name">Ваш e-mail</div>
                                                                <input type="text" class="form-control" name="email" placeholder="Укажите ваш E-mail">
                                                            </div>
                                                            <div class="block_cont">
                                                                <div class="block_cont_name">Ваш номер телефона</div>
                                                                <input type="text" class="form-control" name="phone" placeholder="Укажите телефон для связи">
                                                            </div>
                                                            <div class="block_cont">
                                                                <div class="block_cont_name">Объекты</div>
                                                                <input type="text" class="form-control" name="object" placeholder="Введите название объекта">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>




                                                <div class="form-group row nasos-message">
                                                    <label  class="col-sm-3 col-form-label"><strong>Комментарий</strong></label>
                                                    <div class="col-sm-8">
                                                        <textarea class="form-control" name="message" rows="3" placeholder="При необходимости укажите комментарий к расчёту"></textarea>

                                                    </div>
                                                </div>




                                                <!-- terms -->
                                                <div class="form-group row nasos-kapcha">
                                                    <label class="col-sm-3 col-form-label p-0"></label>

                                                    <div class="col-sm-8">

                                                        {%CAPTCHA%}

                                                    </div>
                                                </div>

                                                <!-- Button  -->
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label"></label>

                                                    <div class="col-sm-8"><button type="submit"  class="but_send_schet but_send_schet_sq">Отправить</button>

                                                    </div>

                                                </div>

                                            </form>


                                        </div>

                                    </div>

                                </div>

                            </div>
                        </article>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>