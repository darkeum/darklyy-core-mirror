<!doctype html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Atri-Energo</title>
    <link rel="shortcut icon" href="/usr/tpl/main/assest/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" media="all" href="<?php echo THEME_CONSTRUCT; ?>css/tooltip.css" />
    <link rel="stylesheet" media="all" href="<?php echo THEME_CONSTRUCT; ?>css/style.css" />
    <style>
		a:hover {
			text-decoration: none !important;
		}
		</style>
    <script>
        window.onbeforeunload = function () {
  return "Точно хочешь закрыть страницу?";
}
    </script>
</head>

<body>
    <div id="loading">
        <div class="cssload-loader">
            <div class="cssload-inner cssload-one"></div>
            <div class="cssload-inner cssload-two"></div>
            <div class="cssload-inner cssload-three"></div>
        </div>
        <div class="loaderText">Загрузка...</div>
    </div>
    <header>
        <div id="logo"><img src="<?php echo THEME_CONSTRUCT; ?>images/logo-default.png" alt="" /></div>
        <button class="btn btnRed calculateCost" onclick="calculateCost()"><i class="fa fa-calculator" aria-hidden="true"></i> Рассчитать проект</button>
        <div id="mapTrash" class="atimate" tooltip="Очистить" flow="down"><i onclick="removeMap()" class="fa fa-trash" aria-hidden="true"></i></div>
        <div style="display: none;" id="myCircuit" class="atimate" tooltip="Мои схемы" flow="down"><i onclick="return location.href = '/selection/my'" class="fa fa-database" aria-hidden="true"></i></div>
        <div style="display: none;" id="standardSolution" class="atimate" tooltip="Типовые решения" flow="down"><i onclick="" class="fa fa-sitemap" aria-hidden="true"></i></div>
        <div id="sbobForma" onclick="svobodnayaForma()" class="atimate" tooltip="Свободная форма" flow="down"><i onclick="" class="fa fa-tasks" aria-hidden="true"></i></div>
    </header>


    <div id="blockLeft">

        <?php foreach($data as $v){ ?>
        <? if($v['menu']){ ?>

            <div class="draggable blockLeftCont" data-type="<?=$v['type']?>">
                <h3>
                    <?=$v['name']?>
                </h3>
                <img class="blockImg" src="<?php echo THEME_CONSTRUCT; ?>images/model/<?=$v['image']?>" data-podpitka="<?=$v['podpitka']?>" data-t="<?=$v['t']?>" data-left="<?=$v['left']?>" data-type="<?=$v['type']?>" data-image="<?=$v['type']?>/<?=$v['image_block']?>"
                    data-h1="<?=$v['h1']?>" data-h2="<?=$v['h2']?>" data-c1="<?=$v['c1']?>" data-c2="<?=$v['c2']?>" alt="" />
                <hr />
            </div>

            <? }} ?>

    </div>
    <div id="blockLine">
        <div class="blockLineKb"></div>
        <div class="blockLineLine"></div>
    </div>
    <div id="blockLineTop"></div>
    <div id="blockRight">

        <div id="blockRightWrap">

            <div id="constructMap">

                <div class="MapStr">
                    <div class="MapBlock1">
                        <div id="heatNetworkUnit">
                            <div id="joiningFeeder">Присоединение к подающему <span>трубопроводу по месту</span></div>
                            <span class="thermalNetwork">Тепловая сеть</span>
                            <div id="attachReverse">Присоединение к обратному <span>трубопроводу по месту</span></div>
                        </div>
                        <div class="heat"></div>
                        <div class="heatColdIndent"></div>
                        <div class="cold"></div>
                    </div>
                    <div class="MapBlockImg blockVvodaDS">
                        <div onclick="modalGenerator('block_vvoda','block_vvoda','',this)" class="MapBlockDrop MapBlockDropInput blockActiveHover">
                            <div class="insertBlockNew insertBlockNull">
                                <div class="iconHover"></div>
                                <span>Вставить блок ввода</span>
                            </div>
                        </div>
                    </div>
                    <div class="MapBlockImg MapBlockDropList">

                        <div class="strongBlock">
                            <div class="MapBlockDrop mmc"></div>
                            <div class="blockSystem"></div>
                        </div>
                    </div>
                </div>

                <div class="clear"></div>

                <svg id="svg-line">
                <line></line>
            </svg>
                <div id="svg-point"></div>
            </div>

        </div>
        <div id="blockOneRightWrap">
            <div class="MapBlockOneImg" style="">
                <div class="mapOneBlockDrop blockActiveHover">
                    <div class="divBotPosition"><i class="fa fa-pencil"></i> <i class="fa fa-trash" onclick="deleteBlockOne(this)"></i></div>
                    <div class="insertBlockNew insertBlockNull" onclick="menuType()">
                        <div class="clickDpMenu insertBlockNewMax"><span onclick="modalGenerator('block_vvoda','','',$(this).parents('.mapOneBlockDrop')); ">Вставить блок ввода</span></div>
                        <div class="clickDpMenu insertBlockNewMax"><span onclick="modalGenerator('block_gvs','','',$(this).parents('.mapOneBlockDrop'))">Вставить блок ГВС</span></div>
                        <div class="clickDpMenu insertBlockNewMax"><span onclick="modalGenerator('block_otopleniya','','',$(this).parents('.mapOneBlockDrop'))">Вставить блок отопления</span></div>
                        <div class="clickDpMenu insertBlockNewMax"><span onclick="modalGenerator('block_podpitky','','',$(this).parents('.mapOneBlockDrop'))">Вставить блок подпитки</span></div>

                        <div class="clickDp">
                            <div class="iconHover"></div><span>Вставить блок</span></div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <?=$modalWindow?>
        <div id="systemMessage"></div>

        <div id="svobodnayaForma">
            <div class="bodyAjax">
                <div id="bodyAjaxFon2">
                    <div id="bodyAjaxFonClass1">Свободная форма!</div>
                    <div id="bodyAjaxFonClass2">Краткое описание свободной формы! Что? Где? Когда?<br />Еще одна строка текста.</div>
                </div>
                <div class="shag fGreen" id="s1">1 Шаг
                    <div class="shafKb" style="display: block;"></div>
                </div>
                <div class="shag" id="s2">2 Шаг
                    <div class="shafKb"></div>
                </div>
                <div class="shag" id="s3">3 Шаг
                    <div class="shafKb"></div>
                </div>
                <div class="shag" id="s4">4 Шаг
                    <div class="shafKb"></div>
                </div>
                <div class="shag" id="s5">5 Шаг</div>
                <div class="forma_podbor_hr"></div>
            </div>
            <div class="shag-content shag-sf-true sf-first sf-active" style="display: block">
                <div class="vibor-form-dv">
                    <div class="vibor-form">
                        <div class="vibor-form-div">
                            <div class="vibor-form-title">Блок ввода:</div>
                            <select onchange="sfBlockVvoda(this)" size="1">
                    <option>0</option>
                    <option selected="selected">1</option>
                    </select>
                        </div>
                        <div class="vibor-form-div">
                            <div class="vibor-form-title">Блок отопления (вентиляции):</div>
                            <select onchange="sfBOV(this)" size="1" name="bovSel">
                    <option>0</option>
                    <option selected="selected">1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                    </select>
                        </div>
                        <div class="vibor-form-div">
                            <div class="vibor-form-title">Блок горячего водоснабжения:</div>
                            <select onchange="sfGVS(this)" size="1" name="gvsSel">
                    <option>0</option>
                    <option selected="selected">1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                    </select>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="dan-pom">
                    <h4>Параметры объекта:</h4>
                    <div>
                        <!-- MapBlockDrop ui-droppable blockActiveHover -->
                        <div class="vhodDanie" tooltip="Ширина помещения"><input type="text" name="sfWidth" placeholder="Ширина" /><span class="dan-pom-span">м.</span></div>
                        <div class="vhodDanie" tooltip="Длина помещения"><input type="text" name="sfLenght" placeholder="Длина" /><span class="dan-pom-span">м.</span></div>
                        <div class="vhodDanie" tooltip="Высота помещения"><input type="text" name="sfHeight" placeholder="Высота" /><span class="dan-pom-span">м.</span></div>
                        <div class="vhodDanie" tooltip="Высота здания"><i class="fa fa-building-o" aria-hidden="true"></i>
                            <input type="text" name="sfHeightBuilding" placeholder="Высота" /><span class="dan-pom-span"><select name="sfHeightBuilding">
                                          <option>Метров</option>
                                          <option>Этажей</option>
                                        </select></span></div>
                        <div>
                            <h4>Наличие выступов, колон:</h4>
                            <div><label><input name="nKolon" value="1" type="radio" /> Да</label> <label><input name="nKolon" value="0" type="radio" checked="checked" /> Нет</label></div>
                            <div class="kolBlockVvDiv">
                                <h4>На сколько систем рассчитан блок ввода?</h4>
                                <div class="vhodDanie">Количество <input type="text" onblur="kolBlockVv(this)" class="kolBlockVv" name="" /></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="shag-content shag-sf-true shag-content-block-vvoda">
                <div class="sf-dop-info">
                    <h3>Блок ввода</h3>
                    <h4>Вводные данные:</h4>
                    <div class="vhodDanie qNagruzkaBv" tooltip="Нагрузка">Q <input class="SFqSum" oninput="" type="text" name="SFq">
                        <select name="SFqType">
                                          <option>Гкал/ч</option>
                                          <option>Ккал/ч</option>
                                          <option>МВт</option>
                                          <option>Вт</option>
                                        </select>
                    </div>

                    <div class="vhodDanie bSFt1" tooltip="Температура в подающем трубопроводе">T1 <input class="SFt1" name="SFt1" type="text"> ℃</div>
                    <div class="vhodDanie bSFt2" tooltip="Температура в обратном трубопроводе">T2 <input type="text" name="SFt2" value=""> ℃</div>
                    <div class="vhodDanie bSFt3" tooltip="Температура в подающем трубопроводе">T3 <input class="SFt3" name="SFt3" type="text"> ℃</div>
                    <div class="vhodDanie bSFt4" tooltip="Температура в обратном трубопроводе">T4 <input type="text" name="SFt4" value=""> ℃</div>
                    <div class="vhodDanie bSFp1" tooltip="Давление в подающем трубопроводе">P1 <input type="text" name="SFp1">
                        <select name="SFp1Type">
                                          <option>МПа</option>
                                          <option>кгс/см²</option>
                                          <option>м.в.ст.</option>
                                        </select>
                    </div>
                    <div class="vhodDanie bSFp2" tooltip="Давление в обратном трубопроводе">P2 <input type="text" name="SFp2">
                        <select name="SFp2Type">
                                          <option>МПа</option>
                                          <option>кгс/см²</option>
                                          <option>м.в.ст.</option>
                                        </select>
                    </div>

                    <div class="vhodDanie bSFp3" tooltip="Давление в подающем трубопроводе">P3 <input type="text" name="SFp3">
                        <select name="SFp3Type">
                                          <option>МПа</option>
                                          <option>кгс/см²</option>
                                          <option>м.в.ст.</option>
                                        </select>
                    </div>
                    <div class="vhodDanie bSFp4" tooltip="Давление в обратном трубопроводе">P4 <input type="text" name="SFp4">
                        <select name="SFp4Type">
                                          <option>МПа</option>
                                          <option>кгс/см²</option>
                                          <option>м.в.ст.</option>
                                        </select>
                    </div>

                    <h4>Конфигурация:</h4>
                    <div class="vhodDanie labPt"><label><input id="uzel_ucheta" type="checkbox"> Узел учета </label></div>
                    <div class="vhodDanie labPt"><label><input id="podpitka" type="checkbox"> Подпитка </label></div>
                    <div class="vhodDanie labPt"><label><input id="regulator_davleniya" onchange="chekInputRg(this)" type="checkbox"> Регулятор давления </label> <input class="" type="text" name="SFregDavVal" disabled="disabled"></div>
                    <div class="vhodDanie labPt"><label><input id="ogranichenie_rashoda" onchange="chekInputRg(this)" type="checkbox"> Ограничение расхода </label> <input class="" type="text" name="SForgRasVal" disabled="disabled"></div>
                    <div class="vhodDanie labPt" tooltip="Ввод в здание"><label><input class="fourTruba" onchange="fourTruba(this)" type="checkbox"> 4-х трубная </label></div>

                </div>
            </div>
            <div class="shag-content shag-sf-true shag-content-bov">
                <div class="sf-dop-info">
                    <h3>Блок отопления (вентиляции):</h3>
                    <div class="modalCharacteristic modal-block-otopleniyac bov">
                        <fieldset>
                            <legend>БОВ-1</legend>
                            <h4 class="bov-h4">Блок:</h4>
                            <form>
                                <div class="nameTypeBlock">
                                    <div class="vhodDanie"><label><input value="1" name="SFtypeBlock" data-name="отопления" type="radio" checked="checked"> отопления </label></div>
                                    <div class="vhodDanie"><label><input value="2" name="SFtypeBlock" data-name="вентиляции" type="radio"> вентиляции </label></div>
                                    <div class="vhodDanie"><label><input value="3" name="SFtypeBlock" data-name="технологии" type="radio"> технологии </label></div>
                                </div>
                            </form>
                            <h4>Вводные данные:</h4>
                            <div class="vhodDanie" tooltip="Нагрузка">Q <input type="text" name="SFq">
                                <select name="SFqType">
                                          <option>Гкал/ч</option>
                                          <option>Ккал/ч</option>
                                          <option>МВт</option>
                                          <option>Вт</option>
                                        </select>
                            </div>
                            <div class="vhodDanie" tooltip="Температура в подающем трубопроводе">T1 <input type="text" name="SFt1"> ℃</div>
                            <div class="vhodDanie" tooltip="Температура в обратном трубопроводе">T2 <input type="text" name="SFt2"> ℃</div>
                            <div class="vhodDanie" tooltip="Давление в подающем трубопроводе">P1 <input type="text" name="SFp1">
                                <select name="SFp1Type">
                                          <option>МПа</option>
                                          <option>кгс/см²</option>
                                          <option>м.в.ст.</option>
                                        </select>
                            </div>
                            <div class="vhodDanie" tooltip="Давление в обратном трубопроводе">P2 <input type="text" name="SFp2">
                                <select name="SFp2Type">
                                          <option>МПа</option>
                                          <option>кгс/см²</option>
                                          <option>м.в.ст.</option>
                                        </select>
                            </div>
                            <div id="t11" class="vhodDanie" tooltip="Температура в подающем трубопроводе отопления">T11 <input type="text" name="SFt11"> ℃</div>
                            <div id="t21" class="vhodDanie" tooltip="Температура в обратном трубопроводе отопления">T21 <input type="text" name="SFt21"> ℃</div>
                            <div id="dp" class="vhodDanie" tooltip="Потери давления в системе отопления">ΔP <input type="text" name="SFdp">
                                <select name="SFdpType">
                                          <option>м.в.ст</option>
                                          <option>кПа</option>
                                          <option>Па</option>
                                        </select>
                            </div>
                            <div id="hVs" class="vhodDanie" tooltip="Высота здания">H <input type="text" name="SFh"> м</div>
                            <h4>Конфигурация:</h4>
                            <div class="vhodDanie" tooltip="Преобразователь частоты">
                                <div class="pch"><span>Насос</span>
                                    <form><label><input id="" onchange="" name="SFpch" value="1" type="radio" checked=""> с ПЧ</label> <label><input id="" onchange="" name="SFpch" value="0" type="radio"> без ПЧ </label></form>
                                </div>
                            </div>
                            <div class="vhodDanie" tooltip="" flow="right">Контроллер <input type="text" name="SFkontr"></div>
                            <div class="vhodDanie" tooltip="" flow="right">Регулирующий клапан <input type="text" name="SFregKlapan"></div>
                            <div class="vhodDanie" tooltip="">Шкаф автоматики <input type="text" name="SFshkafAvtomatiky"></div>

                            <div class="vhodDanie labPt"><label><input class="sf_bp" onchange="sdBP(this)" type="checkbox"> Блок подпитки </label></div>
                            <div class="sfBP">
                                <h4>Блок подпитки:</h4>
                                <div class="vhodDanie labPt" tooltip="Насосная станция"> Насосная станция <input type="text" name="SFnasosStan"></div>
                                <div class="vhodDanie" tooltip="" flow="right">Повысительный насос <input type="text" name="SFnasosPov"></div>
                                <div class="vhodDanie" tooltip="" flow="right">Клапан соленоидный <input type="text" name="SFklapanSol"></div>
                                <div class="vhodDanie" tooltip="" flow="right">Расширительный бак <input type="text" name="SFrasshirBak"></div>

                            </div>
                            <div class="vhodDanie labPt"><label><input class="sf_br" onchange="sdBP(this)" type="checkbox"> Распределительный блок </label></div>
                            <div class="sfBR">
                                <div class="sfBrOtv">
                                    <div>Ответвление №1</div>
                                    <div class="vhodDanie">Ду <input onkeydown="br_n(this)" type="text" name="SFdu"> мм</div>
                                    <div class="vhodDanie labPt"><label><input class="br_klapan" name="br_klapan" type="checkbox"> Балансировочный клапан </label></div>
                                </div>
                            </div>
                            <input class="bvNasos" type="hidden" value="0">
                        </fieldset>
                    </div>
                </div>
            </div>
            <div class="shag-content shag-sf-true shag-content-gvs">
                <div class="sf-dop-info">
                    <h3>Блок горячего водоснабжения:</h3>
                    <div class="modalCharacteristic modal-characteristic-gvs gvs">
                        <fieldset>
                            <legend>ГВС</legend>
                            <h4>Вводные данные:</h4>
                            <div id="rd1" class="vhodDanie" tooltip="Нагрузка">Q <input type="text" name="SFqGVS">
                                <select name="SFqSelGVS">
                                          <option>Гкал/ч</option>
                                          <option>Ккал/ч</option>
                                          <option>МВт</option>
                                          <option>Вт</option>
                                        </select>
                            </div>
                            <div class="vhodDanie" tooltip="Температура в подающем трубопроводе в летний период">T1<span class="gvsLeto">л</span> <input type="text" name="SFGVSt1"> ℃</div>
                            <div class="vhodDanie" tooltip="Температура в обратном трубопроводе в летний период">T2<span class="gvsLeto">л</span> <input type="text" name="SFGVSt2"> ℃</div>
                            <div class="vhodDanie" tooltip="Давление в подающем трубопроводе">P1 <input type="text" name="SFp1">
                                <select name="SFp1Type">
                                          <option>МПа</option>
                                          <option>кгс/см²</option>
                                          <option>м.в.ст.</option>
                                        </select>
                            </div>
                            <div class="vhodDanie" tooltip="Давление в обратном трубопроводе">P2 <input type="text" name="SFp2">
                                <select name="SFp2Type">
                                          <option>МПа</option>
                                          <option>кгс/см²</option>
                                          <option>м.в.ст.</option>
                                        </select>
                            </div>
                            <div class="vhodDanie" tooltip="Температура горячей воды">T3 <input type="text" name="SFGVSt3"> ℃</div>
                            <div class="vhodDanie" tooltip="Температура холодной воды">Tв1 <input type="text" name="SFGVStv1"> ℃</div>
                            <div class="vhodDanie" tooltip="Давление в трубопроводе холодной воды">Pв1 <input type="text" name="SFpb1">
                                <select name="SFpb1SelGVS">
                                          <option>МПа</option>
                                          <option>кгс/см²</option>
                                          <option>м.в.ст.</option>
                                        </select>
                            </div>
                            <h4>Конфигурация</h4>
                            <form>
                                <div class="vhodDanie labPt" tooltip="Схема подключения" flow="right">
                                    <label class="labPd"><input value="1" name="SFshemaPodkl" type="radio" checked="checked" data-name="Согласно СП"> Согласно СП </label>
                                    <label class="labPd"><input value="2" name="SFshemaPodkl" type="radio" data-name="Смешанная"> Параллельная </label>
                                    <label><input value="2" name="SFshemaPodkl" type="radio" data-name="Смешанная"> Смешанная </label></div>
                                <div class="vhodDanie" tooltip="Преобразователь частоты">
                                    <div class="pch"><span>Насос</span> <label><input name="SFpch" value="1" type="radio" checked="checked"> с ПЧ</label> <label><input id="" onchange="" name="SFpch" value="0" type="radio"> без ПЧ </label></div>
                                </div>
                            </form>
                            <div class="vhodDanie" tooltip="" flow="right">Контроллер <input type="text" name="SFkontrGVS"></div>
                            <div class="vhodDanie" tooltip="" flow="right">Регулирующий клапан <input type="text" name="SFregKlapanGVS"></div>
                            <div class="vhodDanie" tooltip="">Шкаф автоматики <input type="text" name="SFshkafAvtomatikyGVS"></div>

                        </fieldset>
                    </div>
                </div>
            </div>

            <div class="shag-content shag-sf-true sf-last">
                <div class="sf-dop-info">
                    <h4>Дополнительная информация:</h4>
                    <textarea name="message"></textarea>
                    <div class="loadFile">
                        <h4>Прикрепить файлы:</h4>
                        <div class="fileLoadBlock">
                            <div class="fileLoadBlockDiv">1. <input type="file" name="userfile" class="userfile"></div>
                        </div>
                        <input class="botLoadFileName" onclick="botLoadFileName()" type="button" value="+" />
                    </div>
                </div>
            </div>

            <div class="submiltLoader">
                <div class="raschetStoimostyLoading">
                    <div class="cssload-loader">
                        <div class="cssload-inner cssload-one"></div>
                        <div class="cssload-inner cssload-two"></div>
                        <div class="cssload-inner cssload-three"></div>
                    </div>
                    <p class="uploaded"></p>
                    <progress max="100" value="0" class="progress"></progress>
                    <p class="result"></p>
                    <div class="loaderText">Отправка данных...</div>
                </div>
            </div>

            <div class="sf-ok">
                <div class="sf-ok-div">
                    <div class="sf-icon-ok"><i class="fa fa-check" aria-hidden="true"></i></div>
                    <div class="sf-Text">Форма успешно отправлена! </div>
                </div>
            </div>

            <div class="sf-error">
                <div class="sf-error-div">
                    <div class="sf-icon-error"><i class="fa fa-times" aria-hidden="true"></i></div>
                    <div class="sf-Text">Произошла ошибка! </div>
                </div>
            </div>

            <div class="modal-form-bot">
                <div class="modal-form-bot-left" onclick="sbPrev()">Назад</div>
                <div class="modal-form-bot-right" onclick="sbNext()">Далее</div>
                <div class="modal-form-bot-save" onclick="adq($('.loadFile'))">Отправить</div>
            </div>
        </div>

        <div id="raschetStoimostySource">
            <div class="raschetStoimostyLoading">
                <div class="cssload-loader">
                    <div class="cssload-inner cssload-one"></div>
                    <div class="cssload-inner cssload-two"></div>
                    <div class="cssload-inner cssload-three"></div>
                </div>
                <div class="loaderText">Идёт подбор оборудования. Ожидайте...</div>
                <div class="loaderTextTimer"></div>
            </div>
            <div class="raschetStoimostyResult"></div>
            <div class="modalBottom resultModalBot"><button type="button" class="btn btn-dark arcticmodal-close">Отмена</button><button type="button" onclick="window.open('/selection/my');" class="btn btn-danger">Мои расчеты</button></div>

        </div>
        <div class="blockNone">
            <div class="blockPodpitkyList" onclick="modalGenerator('block_podpitky','','',this)">
                <div class="insertBlockPodpitky">
                    <div class="iconHover"></div><span>Вставить блок подпитки</span></div>
            </div>
        </div>

        <span id="i-data" class="spanHidden"></span>
        <span id="i-data-general" class="spanHidden"></span>
        <span id="i-data-block" class="spanHidden"></span>
        <span id="i-q" class="spanHidden"></span>
        <input id="st-i-nasos" type="hidden" value="0">

        <link rel="stylesheet" href="/usr/tpl/dev/assest/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo THEME_CONSTRUCT; ?>css/jquery.arcticmodal-0.3.css">
        <link rel="stylesheet" href="<?php echo THEME_CONSTRUCT; ?>css/simple.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="<?php echo THEME_CONSTRUCT; ?>js/jquery-ui.min.js"></script>
        <script src="<?php echo THEME_CONSTRUCT; ?>js/jquery.nicescroll.min.js"></script>
        <script src="<?php echo THEME_CONSTRUCT; ?>js/jquery.arcticmodal-0.3.min.js"></script>
        <script src="<?php echo THEME_CONSTRUCT; ?>js/jquery.cookie.min.js"></script>
        <script src="<?php echo THEME_CONSTRUCT; ?>js/jquery.ui.touch-punch.js"></script>
        <script src='<?php echo THEME_CONSTRUCT; ?>js/scripts.js?v2'></script>
        <script src='<?php echo THEME_CONSTRUCT; ?>js/vetka2.js'></script>
        <script src='<?php echo THEME_CONSTRUCT; ?>js/uploader-setup.js'></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
        <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
        <style>
            .aspect-tab-content table,
            .aspect-tab-content th,
            .aspect-tab-content td {
                border: 1px solid rgba(0, 0, 0, 0.1);
                font-size: 16px;
            }
            
            .aspect-tab-content table {
                border-collapse: separate;
                border-spacing: 0px;
                border-width: 1px 0 0 1px;
                margin-bottom: 24px;
                width: 100%;
            }
            
            .aspect-tab-content caption,
            .aspect-tab-content th,
            .aspect-tab-content td {
                font-weight: normal;
                text-align: left;
            }
            
            .aspect-tab-content th {
                border-width: 0 1px 1px 0;
                font-weight: bold;
                line-height: 24px;
                display: table-cell;
                vertical-align: middle;
                text-align: left;
                padding: 10px
            }
            
            .aspect-tab-content td {
                border-width: 0 1px 1px 0;
                padding: 10px;
            }
            
            .aspect-tab-content tr:nth-child(odd) {
                background: #EEE;
            }
            
            #aspect-content {
                margin: 50px 0 0;
                font-family: "Poppins", sans-serif;
            }
            
            #aspect-content * {
                box-sizing: border-box;
            }
            
            .aspect-tab {
                position: relative;
                width: 100%;
                max-width: 1800px;
                margin: 0 auto 10px;
                border-radius: 4px;
                background-color: #fff;
                box-shadow: 0 0 0 1px #ececec;
                opacity: 1;
                transition: box-shadow 0.2s, opacity 0.4s;
            }
            
            .aspect-tab:hover {
                box-shadow: 0 4px 10px 0 rgba(0, 0, 0, 0.11);
            }
            
            .aspect-input {
                display: none;
            }
            
            .aspect-input:checked~.aspect-content+.aspect-tab-content {
                max-height: 3000px;
            }
            
            .aspect-input:checked~.aspect-content:after {
                transform: rotate(0);
            }
            
            .aspect-label {
                position: absolute;
                top: 0;
                left: 0;
                height: 100%;
                max-height: 80px;
                width: 100%;
                margin: 0;
                padding: 0;
                font-size: 0;
                z-index: 1;
                cursor: pointer;
            }
            
            .aspect-label:hover~.aspect-content:after {
                background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0Ij4KICAgIDxnIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPHBhdGggZD0iTTI0IDI0SDBWMGgyNHoiIG9wYWNpdHk9Ii44NyIvPgogICAgICAgIDxwYXRoIGZpbGw9IiM1NTZBRUEiIGZpbGwtcnVsZT0ibm9uemVybyIgZD0iTTE1Ljg4IDE1LjI5TDEyIDExLjQxbC0zLjg4IDMuODhhLjk5Ni45OTYgMCAxIDEtMS40MS0xLjQxbDQuNTktNC41OWEuOTk2Ljk5NiAwIDAgMSAxLjQxIDBsNC41OSA0LjU5Yy4zOS4zOS4zOSAxLjAyIDAgMS40MS0uMzkuMzgtMS4wMy4zOS0xLjQyIDB6Ii8+CiAgICA8L2c+Cjwvc3ZnPgo=");
            }
            
            .aspect-content {
                position: relative;
                display: block;
                height: 80px;
                margin: 0;
                padding: 0 87px 0 30px;
                font-size: 0;
                white-space: nowrap;
                cursor: pointer;
                user-select: none;
                -webkit-user-select: none;
                -khtml-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                -o-user-select: none;
            }
            
            .aspect-content:before,
            .aspect-content:after {
                content: "";
                display: inline-block;
                vertical-align: middle;
            }
            
            .aspect-content:before {
                height: 100%;
            }
            
            .aspect-content:after {
                position: absolute;
                width: 24px;
                height: 100%;
                right: 30px;
                background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0Ij4KICAgIDxnIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPHBhdGggZD0iTTI0IDI0SDBWMGgyNHoiIG9wYWNpdHk9Ii44NyIvPgogICAgICAgIDxwYXRoIGZpbGw9IiNBOUFDQUYiIGZpbGwtcnVsZT0ibm9uemVybyIgZD0iTTE1Ljg4IDE1LjI5TDEyIDExLjQxbC0zLjg4IDMuODhhLjk5Ni45OTYgMCAxIDEtMS40MS0xLjQxbDQuNTktNC41OWEuOTk2Ljk5NiAwIDAgMSAxLjQxIDBsNC41OSA0LjU5Yy4zOS4zOS4zOSAxLjAyIDAgMS40MS0uMzkuMzgtMS4wMy4zOS0xLjQyIDB6Ii8+CiAgICA8L2c+Cjwvc3ZnPgo=");
                background-repeat: no-repeat;
                background-position: center;
                transform: rotate(180deg);
            }
            
            .aspect-name {
                display: inline-block;
                width: 75%;
                margin-left: 16px;
                font-weight: 500;
                color: #242a32;
                white-space: normal;
                text-align: left;
                vertical-align: middle;
            }
            
            .aspect-stat {
                width: 40%;
                text-align: right;
            }
            
            .all-opinions,
            .aspect-name {
                font-size: 14px;
                line-height: 22px;
            }
            
            .all-opinions {
                color: #5d5d5d;
                text-align: left;
            }
            
            .aspect-content+.aspect-tab-content {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s;
            }
            
            .aspect-content>div,
            .aspect-stat>div {
                display: inline-block;
            }
            
            .aspect-content>div {
                vertical-align: middle;
            }
            
            .positive-count,
            .negative-count,
            .neutral-count {
                display: inline-block;
                margin: 0 0 0 20px;
                padding-left: 26px;
                background-repeat: no-repeat;
                font-size: 13px;
                line-height: 20px;
                color: #363636;
            }
            
            .positive-count {
                background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgdmlld0JveD0iMCAwIDIwIDIwIj4KICAgIDxwYXRoIGZpbGw9IiM3RUQzMjEiIGZpbGwtcnVsZT0iZXZlbm9kZCIgZD0iTTEwIDE4LjU3MWMtNC43MjYgMC04LjU3MS0zLjg0NS04LjU3MS04LjU3MSAwLTQuNzI2IDMuODQ1LTguNTcxIDguNTcxLTguNTcxIDQuNzI2IDAgOC41NzEgMy44NDUgOC41NzEgOC41NzEgMCA0LjcyNi0zLjg0NSA4LjU3MS04LjU3MSA4LjU3MXpNMjAgMTBjMCA1LjUxNC00LjQ4NiAxMC0xMCAxMFMwIDE1LjUxNCAwIDEwIDQuNDg2IDAgMTAgMHMxMCA0LjQ4NiAxMCAxMHpNNSAxMS40MjdhNSA1IDAgMCAwIDEwIDAgLjcxNC43MTQgMCAxIDAtMS40MjkgMCAzLjU3MSAzLjU3MSAwIDAgMS03LjE0MiAwIC43MTQuNzE0IDAgMSAwLTEuNDI5IDB6bTEuMDcxLTVhMS4wNzEgMS4wNzEgMCAxIDAgMCAyLjE0MyAxLjA3MSAxLjA3MSAwIDAgMCAwLTIuMTQzem03Ljg1OCAwYTEuMDcxIDEuMDcxIDAgMSAwIDAgMi4xNDMgMS4wNzEgMS4wNzEgMCAwIDAgMC0yLjE0M3oiLz4KPC9zdmc+Cg==");
            }
            
            .negative-count {
                background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgdmlld0JveD0iMCAwIDIwIDIwIj4KICAgIDxwYXRoIGZpbGw9IiNGRjZFMDAiIGZpbGwtcnVsZT0iZXZlbm9kZCIgZD0iTTEwIDE4LjU3MWMtNC43MjYgMC04LjU3MS0zLjg0NS04LjU3MS04LjU3MSAwLTQuNzI2IDMuODQ1LTguNTcxIDguNTcxLTguNTcxIDQuNzI2IDAgOC41NzEgMy44NDUgOC41NzEgOC41NzEgMCA0LjcyNi0zLjg0NSA4LjU3MS04LjU3MSA4LjU3MXpNMjAgMTBjMCA1LjUxNC00LjQ4NiAxMC0xMCAxMFMwIDE1LjUxNCAwIDEwIDQuNDg2IDAgMTAgMHMxMCA0LjQ4NiAxMCAxMHpNNSAxNC45OThhLjcxNC43MTQgMCAwIDAgMS40MjkgMCAzLjU3MSAzLjU3MSAwIDAgMSA3LjE0MiAwIC43MTQuNzE0IDAgMSAwIDEuNDI5IDAgNSA1IDAgMSAwLTEwIDB6bTEuMDcxLTguNTdhMS4wNzEgMS4wNzEgMCAxIDAgMCAyLjE0MiAxLjA3MSAxLjA3MSAwIDAgMCAwLTIuMTQzem03Ljg1OCAwYTEuMDcxIDEuMDcxIDAgMSAwIDAgMi4xNDIgMS4wNzEgMS4wNzEgMCAwIDAgMC0yLjE0M3oiLz4KPC9zdmc+Cg==");
            }
            
            .neutral-count {
                background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgdmlld0JveD0iMCAwIDIwIDIwIj4KICAgIDxwYXRoIGZpbGw9IiNCQUMyRDYiIGZpbGwtcnVsZT0iZXZlbm9kZCIgZD0iTTEwIDE4LjU3MWMtNC43MjYgMC04LjU3MS0zLjg0NS04LjU3MS04LjU3MSAwLTQuNzI2IDMuODQ1LTguNTcxIDguNTcxLTguNTcxIDQuNzI2IDAgOC41NzEgMy44NDUgOC41NzEgOC41NzEgMCA0LjcyNi0zLjg0NSA4LjU3MS04LjU3MSA4LjU3MXpNMjAgMTBjMCA1LjUxNC00LjQ4NiAxMC0xMCAxMFMwIDE1LjUxNCAwIDEwIDQuNDg2IDAgMTAgMHMxMCA0LjQ4NiAxMCAxMHpNNS43MTQgMTEuNDI3YS43MTQuNzE0IDAgMSAwIDAgMS40MjloOC41NzJhLjcxNC43MTQgMCAxIDAgMC0xLjQyOUg1LjcxNHptLjM1Ny01YTEuMDcxIDEuMDcxIDAgMSAwIDAgMi4xNDMgMS4wNzEgMS4wNzEgMCAwIDAgMC0yLjE0M3ptNy44NTggMGExLjA3MSAxLjA3MSAwIDEgMCAwIDIuMTQzIDEuMDcxIDEuMDcxIDAgMCAwIDAtMi4xNDN6Ii8+Cjwvc3ZnPgo=");
            }
            
            .aspect-info {
                width: 60%;
                white-space: nowrap;
                font-size: 0;
            }
            
            .aspect-info:before {
                content: "";
                display: inline-block;
                height: 44px;
                vertical-align: middle;
            }
            
            .chart-pie {
                position: relative;
                display: inline-block;
                height: 44px;
                width: 44px;
                border-radius: 50%;
                background-color: #e4e4e4;
                vertical-align: middle;
            }
            
            .chart-pie:after {
                content: "";
                display: block;
                position: absolute;
                height: 40px;
                width: 40px;
                top: 2px;
                left: 2px;
                border-radius: 50%;
                background-color: #fff;
            }
            
            .chart-pie-count {
                position: absolute;
                display: block;
                height: 100%;
                width: 100%;
                font-size: 14px;
                font-weight: 500;
                line-height: 44px;
                color: #242a32;
                text-align: center;
                z-index: 1;
            }
            
            .chart-pie>div {
                clip: rect(0, 44px, 44px, 22px);
            }
            
            .chart-pie>div,
            .chart-pie.over50 .first-fill {
                position: absolute;
                height: 44px;
                width: 44px;
                border-radius: 50%;
            }
            
            .chart-pie.over50>div {
                clip: rect(auto, auto, auto, auto);
            }
            
            .chart-pie.over50 .first-fill {
                clip: rect(0, 44px, 44px, 22px);
            }
            
            .chart-pie:not(.over50) .first-fill {
                display: none;
            }
            
            .second-fill {
                position: absolute;
                clip: rect(0, 22px, 44px, 0);
                width: 100%;
                height: 100%;
                border-radius: 50%;
                border-width: 3px;
                border-style: solid;
                box-sizing: border-box;
            }
            
            .chart-pie.positive .first-fill {
                background-color: #82d428;
            }
            
            .chart-pie.positive .second-fill {
                border-color: #82d428;
            }
            
            .chart-pie.negative .first-fill {
                background-color: #ff6e00;
            }
            
            .chart-pie.negative .second-fill {
                border-color: #ff6e00;
            }
            
            .aspect-tab-content {
                background-color: #f9f9f9;
                font-size: 0;
                text-align: justify;
            }
            
            .sentiment-wrapper {
                padding: 24px 30px 30px;
            }
            
            .sentiment-wrapper>div {
                display: inline-block;
                width: 100%;
                padding: 0 5px;
                box-sizing: border-box;
                vertical-align: top;
                cursor: default;
            }
            
            .sentiment-wrapper>div>div {
                width: 100%;
                padding: 16px 24px 20px;
                box-sizing: border-box;
                border-radius: 4px;
                background-color: #fff;
                border: 1px solid #ececec;
                text-align: left;
            }
            
            .opinion-header {
                position: relative;
                width: 100%;
                margin: 0 0 24px;
                font-size: 13px;
                font-weight: 500;
                line-height: 20px;
                color: #242a32;
                text-transform: capitalize;
            }
            
            .opinion-header>span:nth-child(2) {
                position: absolute;
                right: 0;
            }
            
            .opinion-header+div>span {
                font-size: 13px;
                font-weight: 400;
                line-height: 22px;
                color: #363636;
            }
            
            @media screen and (max-width: 800px) {
                .aspect-label {
                    max-height: 102px;
                }
                .aspect-content {
                    height: auto;
                    padding: 10px 87px 10px 30px;
                }
                .aspect-content:before {
                    display: none;
                }
                .aspect-content:after {
                    top: 0;
                }
                .aspect-content>div {
                    display: block;
                    width: 100%;
                }
                .aspect-stat {
                    margin-top: 10px;
                    text-align: left;
                }
            }
            
            @media screen and (max-width: 750px) {
                .sentiment-wrapper>div {
                    display: block;
                    width: 100%;
                    max-width: 100%;
                }
                .sentiment-wrapper>div:not(:first-child) {
                    margin-top: 10px;
                }
            }
            
            @media screen and (max-width: 500px) {
                .aspect-label {
                    max-height: 140px;
                }
                .aspect-stat>div {
                    display: block;
                    width: 100%;
                }
                .all-opinions {
                    margin-bottom: 10px;
                }
                .all-opinions+div>span:first-child {
                    margin: 0;
                }
            }
        </style>
</body>

</html>