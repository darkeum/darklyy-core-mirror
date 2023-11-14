<?php
class constructor {
    
    public function fnConstructor(){
		$mySql = new mySql; 
        $data = $mySql->query("SELECT * FROM constructor_block");




        // Генерация разметки модальных окон

        $modalWindow = '<div class="box-modal-div">';

        foreach ($data as $v){

            if($v['menu']) {

                $modalWindow .= '<div id="' . $v['type'] . '" class="box-content">';
                $modalWindow .= '<h3>' . $v['name'] . '</h3>';


                // Характеристики рассчетов

                $modalWindow .= '<div class="modalCharacteristic" data-type="' . $v['type'] . '">';

                if ($v['type'] == 'block_vvoda') {

                    $modalWindow .= '<fieldset><legend>Вводные данные</legend>';

                    $modalWindow .= '<div class="vhodDanie" tooltip="Нагрузка">Q <input class="qSum" oninput="inputCheck(this)" type="text" name="q" />
                                        <select name="qType">
                                          <option>Гкал/ч</option>
                                          <option>Ккал/ч</option>
                                          <option>МВт</option>
                                          <option>Вт</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="Температура в подающем трубопроводе">T1 
                                     <select name="t1">
                                          <option>150</option>
                                          <option>130</option>
                                          <option>115</option>
                                          <option>110</option>
                                          <option>105</option>
                                          <option>95</option>
                                      </select>
                                      &#8451</div>
                                     <div class="vhodDanie" tooltip="Температура в обратном трубопроводе">T2 <input oninput="inputCheck(this)" type="text" name="t2" value="70" disabled="disabled" /> &#8451;</div>
                                     <div class="vhodDanie" tooltip="Давление в подающем трубопроводе">P1 <input oninput="inputCheck(this)" type="text" name="p1" />
                                        <select name="p1Type">
                                          <option>кгс/см²</option>
                                          <option>МПа</option>
                                          <option>м.в.ст.</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="Давление в обратном трубопроводе">P2 <input oninput="inputCheck(this)" type="text" name="p2" />
                                        <select name="p2Type">
                                          <option>кгс/см²</option>
                                          <option>МПа</option>
                                          <option>м.в.ст.</option>
                                        </select>
                                     </div>';

                    $modalWindow .= '</fieldset>';


                    $modalWindow .= '<fieldset class="configurBlockVvoda"><legend>Конфигурация</legend>
                                     <div class="vhodDanie labPt"><label><input id="uzel_ucheta" onchange="check(this)" type="checkbox" checked /> Узел учета </label></div>
                                     <div class="vhodDanie labPt"><label><input id="regul_perepad" onchange="check(this)" type="checkbox" checked /> Регулятор перепада </label></div>
                                     <div class="vhodDanie labPt"><label><input id="bal_klapan" onchange="check(this)" type="checkbox" checked /> Балансировочный клапан </label></div>';

                    $modalWindow .= '</fieldset>';


                }


                if ($v['type'] == 'block_otopleniya' || $v['type'] == 'block_podpitky') {

                    $modalWindow .= '<fieldset><legend>Блок</legend>';

                    $modalWindow .= '<div class="nameTypeBlock"><div class="vhodDanie"><label><input id="" onchange="typeBlock(this)" value="1" name="typeBlock" data-name="отопления" type="radio" checked /> отопления </label></div>
                                     <div class="vhodDanie"><label><input id="" onchange="typeBlock(this)" value="2" name="typeBlock" data-name="вентиляции" type="radio" /> вентиляции </label></div>
                                     <div class="vhodDanie"><label><input id="" onchange="typeBlock(this)" value="3" name="typeBlock" data-name="технологии" type="radio" /> технологии </label></div>';

                    $modalWindow .= '</div></fieldset>';


                    if ($v['type'] == 'block_otopleniya') {

                        $modalWindow .= '<fieldset><legend>Вводные данные</legend>';

                        $modalWindow .= '<div class="vhodDanie" tooltip="Нагрузка">Q <input type="text" oninput="inputCheck(this)" name="q" />
                                        <select name="qType">
                                          <option>Гкал/ч</option>
                                          <option>Ккал/ч</option>
                                          <option>МВт</option>
                                          <option>Вт</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="Температура в подающем трубопроводе">T1 
                                     <select name="t1" onchange="selT1(this)">
                                          <option>150</option>
                                          <option>130</option>
                                          <option>115</option>
                                          <option>110</option>
                                          <option>105</option>
                                          <option>95</option>
                                      </select>
                                      &#8451
                                      </div>
                                     <div class="vhodDanie" tooltip="Температура в обратном трубопроводе">T2 <input type="text" oninput="inputCheck(this)" name="t2" value="70" disabled="disabled" /> &#8451;</div>
                                     <div class="vhodDanie" tooltip="Давление в подающем трубопроводе">P1 <input type="text" oninput="inputCheck(this)" name="p1" />
                                        <select name="p1Type">
                                          <option>кгс/см²</option>
                                          <option>МПа</option>
                                          <option>м.в.ст.</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="Давление в обратном трубопроводе">P2 <input type="text" oninput="inputCheck(this)" name="p2" />
                                        <select name="p2Type">
                                          <option>кгс/см²</option>
                                          <option>МПа</option>
                                          <option>м.в.ст.</option>
                                        </select>
                                     </div>
                                     <div id="t11" class="vhodDanie" tooltip="Температура в подающем трубопроводе отопления">T11 
                                     <select name="t11" onchange="selT11(this)">
                                          <option>95</option>
                                          <option>90</option>
                                          <option>85</option>
                                          <option>80</option>
                                      </select> &#8451</div>
                                     <div id="t21" class="vhodDanie" tooltip="Температура в обратном трубопроводе отопления">T21 
                                     <select name="t21" onchange="selT21(this)">
                                          <option>70</option>
                                          <option>65</option>
                                          <option>60</option>
                                      </select> &#8451</div>
                                     <div id="dp" class="vhodDanie" tooltip="Потери давления в системе отопления">&#916;P <input type="text" oninput="inputCheck(this)" name="dp" />
                                        <select name="dpType">
                                          <option>м.в.ст</option>
                                          <option>кПа</option>
                                          <option>Па</option>
                                        </select>
                                     </div>
                                     <div id="hVs" class="vhodDanie" tooltip="Высота здания">H <input type="text" oninput="inputCheck(this)" name="h" /> м</div>
                                     ';

                        $modalWindow .= '</fieldset>';

                    }


                    if ($v['type'] == 'block_otopleniya') {


                        $modalWindow .= '<fieldset class="configurBlockVvoda"><legend>Конфигурация</legend>   
                                     <div class="vhodDanie" tooltip="Преобразователь частоты"><div class="pch"><span>Насос</span> <label><input id="" onchange="" name="pch" value="1" type="radio" checked /> с ПЧ</label> <label><input id="" onchange="" name="pch" value="0" type="radio" /> без ПЧ </label></div></div>
                                     <div class="vhodDanie" tooltip="" flow="right">Контроллер
                                        <select name="kontr">
                                          <option>Danfoss</option>
                                          <option>Siemens</option>
                                          <option>Segnetics</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="" flow="right">Регулирующий клапан
                                        <select name="regKlapan">
                                          <option>Danfoss</option>
                                          <option>IMI Hydronic</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="">Шкаф автоматики 
                                        <select name="shkafAvtomatiky">
                                          <option>На раме</option>
                                          <option>На стене</option>
                                          <option>Учтен в БОВ-1</option>
                                        </select>
                                     </div>
                                     ';

                        $modalWindow .= '</fieldset>';

                    }

                    if ($v['type'] == 'block_podpitky') {


                        $modalWindow .= '<fieldset class="configurBlockVvoda"><legend>Конфигурация</legend>   
                                         <div class="vhodDanie labPt" tooltip="Насосная станция"><label class="labPd"><input id="" onchange="" value="2" name="nasosStan" type="radio" checked /> Согласно СП </label>
                                                            <label class="labPd"><input id="" onchange="" value="1" name="nasosStan" type="radio" /> Да </label>
                                                            <label><input id="" onchange="" value="0" name="nasosStan" type="radio" /> Нет </label></div>
                                         <div class="vhodDanie" tooltip="" flow="right">Повысительный насос
                                            <select name="povNasos">
                                              <option>MHI</option>
                                            </select>
                                         </div>
                                         <div class="vhodDanie" tooltip="" flow="right">Клапан соленоидный
                                            <select name="klapanSol">
                                              <option>Tork</option>
                                              <option>Danfoss</option>
                                            </select>
                                         </div>
                                         <div class="vhodDanie" tooltip="" flow="right">Расширительный бак
                                            <select name="rasshirBak">
                                              <option>Wester</option>
                                              <option>Flamco</option>
                                              <option>Reflex</option>
                                            </select>
                                         </div>
                                         ';

                        $modalWindow .= '</fieldset>';

                    }


                }


                if ($v['type'] == 'block_gvs') {


                    $modalWindow .= '<fieldset><legend>Вводные данные</legend>';

                    $modalWindow .= '<div class="vhodDanie labPt"><label class="labQ"><input onchange="typeNag(this)" value="1" name="typeNag" type="radio" checked /> Q</label> 
                                                                  <label><input id="" onchange="typeNag(this)" value="2" name="typeNag" type="radio" /> Gтз</label></div>
                                     <div id="rd1" class="vhodDanie" tooltip="Нагрузка">Q <input type="text" oninput="inputCheck(this)" name="qGVS" />
                                        <select name="qSelGVS">
                                          <option>Гкал/ч</option>
                                          <option>Ккал/ч</option>
                                          <option>МВт</option>
                                          <option>Вт</option>
                                        </select>
                                     </div>
                                     <div id="rd2" class="vhodDanie" tooltip="Расход горячей воды">Gтз <input type="text" oninput="inputCheck(this)" name="GVSgt1" /> м&#179;/ч</div>
                                     <div class="vhodDanie" tooltip="Температура в подающем трубопроводе в летний период">T1<span class="gvsLeto">л</span> <input type="text" oninput="inputCheck(this)" name="GVSt1" value="70" disabled="disabled" /> &#8451</div>
                                     <div class="vhodDanie" tooltip="Температура в обратном трубопроводе в летний период">T2<span class="gvsLeto">л</span> <input type="text" oninput="inputCheck(this)" name="GVSt2" value="30" disabled="disabled" /> &#8451;</div>
                                     <div class="vhodDanie" tooltip="Давление в подающем трубопроводе">P1 <input type="text" oninput="inputCheck(this)" name="p1" />
                                        <select name="p1Type">
                                          <option>кгс/см²</option>
                                          <option>МПа</option>
                                          <option>м.в.ст.</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="Давление в обратном трубопроводе">P2 <input type="text" oninput="inputCheck(this)" name="p2" />
                                        <select name="p2Type">
                                          <option>кгс/см²</option>
                                          <option>МПа</option>
                                          <option>м.в.ст.</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="Температура горячей воды">T3 <input type="text" oninput="inputCheck(this)" value="65" name="GVSt3" value="65" disabled="disabled" /> &#8451</div>
                                     <div class="vhodDanie" tooltip="Температура холодной воды">Tв1 <input type="text" oninput="inputCheck(this)" value="5" name="GVStv1" value="5" disabled="disabled" /> &#8451</div>
                                     <div class="vhodDanie" tooltip="Давление в трубопроводе холодной воды">Pв1 <input type="text" oninput="inputCheck(this)" name="pb1" />
                                        <select name="pb1SelGVS">
                                          <option>кгс/см²</option>
                                          <option>МПа</option>
                                          <option>м.в.ст.</option>
                                        </select>
                                     </div>
                                     ';

                    $modalWindow .= '</fieldset>';


                    $modalWindow .= '<fieldset><legend>Конфигурация</legend>';

                    $modalWindow .= '<div class="vhodDanie labPt" tooltip="Схема подключения" flow="right"><label class="labPd"><input id="" onchange="" value="1" name="shemaPodkl" type="radio" checked /> Согласно СП </label>
                                                            <label class="labPd"><input id="" onchange="" value="2" name="shemaPodkl" type="radio" /> Параллельная </label>
                                                            <label><input id="" onchange="" value="3" name="shemaPodkl" type="radio" /> Смешанная </label></div>
                                     <div class="vhodDanie" tooltip="" flow="right">Контроллер
                                        <select name="kontrGVS">
                                          <option>Danfoss</option>
                                          <option>Siemens</option>
                                          <option>Segnetics</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="" flow="right">Регулирующий клапан
                                        <select name="regKlapanGVS">
                                          <option>Danfoss</option>
                                          <option>IMI Hydronic</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="">Шкаф автоматики 
                                        <select name="shkafAvtomatikyGVS">
                                          <option>На раме</option>
                                          <option>На стене</option>
                                          <option>Учтен в БОВ-1</option>
                                        </select>
                                     </div>
                                     
                                     ';


                    $modalWindow .= '</fieldset>';


                }


                // Узел учета
                // Регулятор перепада
                // Балансировочный клапан
                //

                // Кнопки модальных окон

                $modalWindow .= '<div class="blockPsCarusel"><div class="carouselPrev"><i class="fa fa-chevron-left" onclick="caruselPrevNext(this, \'prev\')" aria-hidden="true"></i></div><div class="carouselNext"><i class="fa fa-chevron-right" onclick="caruselPrevNext(this, \'next\')" aria-hidden="true"></i></div><div class="carousel"><div class="carousel-dv">';


                foreach ($data as $vCarusel) {


                    if ($v['type'] == $vCarusel['type']) {

                        $modalWindow .= '<div onclick="caruselClickActive(this)" class="carousel-element" data-bal_klapan="' . $vCarusel['bal_klapan'] . '" data-regul_perepad="' . $vCarusel['regul_perepad'] . '" data-uzel_ucheta="' . $vCarusel['uzel_ucheta'] . '" data-line="' . $vCarusel['line'] . '" data-nasos="' . $vCarusel['nasos'] . '" data-id="' . $vCarusel['id'] . '"><img class="blockImg" src="'. THEME_CONSTRUCT.'images/' . $vCarusel['type'] . '/' . $vCarusel['image_block'] . '" data-left="' . $vCarusel['left'] . '" data-t="' . $vCarusel['t'] . '" data-podpitka="' . $vCarusel['podpitka'] . '" data-line="' . $vCarusel['line'] . '" data-id="' . $vCarusel['id'] . '" data-type="' . $vCarusel['type'] . '" data-image="'. THEME_CONSTRUCT.'images/'. $vCarusel['type'] . '/' . $vCarusel['image_block'] . '" data-h1="' . $vCarusel['h1'] . '" data-h2="' . $vCarusel['h2'] . '" data-c1="' . $vCarusel['c1'] . '" data-c2="' . $vCarusel['c2'] . '" alt=""/></div>';

                    }


                }

                $modalWindow .= '</div></div></div></div>';

                ////////////////
                // Данные шкафов
                ////////////////

                if ($v['type'] == 'block_vvoda') {


                    $modalWindow .= '<fieldset class="configurBlockVvoda blockVvodaTeplovich"><legend>Тепловычислитель</legend>
                                         <div>
                                         <div class="vhodDanie labPt"><label><input id="shkafPodpitka" onchange="" type="checkbox" /> Подпиточный трубопровод в отдельной теплосистеме </label></div>
                                         </div><div>
                                         <div class="vhodDanie labPt"><label><input id="regul_perepad" onchange="shkafDpInter(this)" type="checkbox" /> Дополнительный интерфейс </label></div>
                                         <div id="skafDi" class="vhodDanie radioStl"><label><input id="" onchange="" value="1" name="skafDi" data-name="" type="radio" checked /> RS 485 </label>
                                         <label><input id="" onchange="" value="2" name="skafDi" data-name="" type="radio" /> Ethernet </label></div>
                                         </div>
                                         <div>
                                         <div class="vhodDanie labPt"><label><input id="shkafDopObor" onchange="shkafDpOborudovanie(this)" type="checkbox" /> Дополнительное оборудование </label></div>
                                         <div id="shkafDpo" class="vhodDanie radioStl"><label><input id="" onchange="" value="1" name="skafDo" data-name="" type="radio" checked /> Модем с SD каналом </label>
                                         <label><input id="" onchange="" value="2" name="skafDo" data-name="" type="radio" />  Модем с GPRS каналом  </label></div>
                                         </div>';

                    $modalWindow .= '</fieldset>';


                }





                $modalWindow .= '<div class="errorText">Заполните все необходимые поля!</div>';

                $modalWindow .= '<div class="modalBottom"><button type="button" class="btn btn-dark arcticmodal-close">Отмена</button><button type="button" onclick="modalSave(this)" class="btn btn-danger">Применить</button></div>';

                $modalWindow .= '</div>';


            }

        }

        $modalWindow .= '</div>';

        require_once(ROOT_CONTRUCTOR.'/tema/constructor.tpl');


    }

}
?>