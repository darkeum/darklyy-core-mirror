<?php
class constructor{
    
    public function fnConstructor(){
		$mySql = new mySql; 
        $data = $mySql->query("SELECT * FROM constructor_block WHERE nasos = '0' AND moder = '1'");


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

                    $modalWindow .= '<div class="vhodDanie" tooltip="Нагрузка (учитывается в других блоках)" style="display: none;">Q 
                                        <input class="qSum" oninput="inputCheck(this)" type="number" value="0" name="q" disabled />
                                        <select name="qType">
                                          <option>Гкал/ч</option>
                                          <option>Ккал/ч</option>
                                          <option>МВт</option>
                                          <option>Вт</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="Температура в подающем трубопроводе">T1 
                                     <input oninput="inputCheck(this)" type="number" name="t1" value="150"  /> &#8451;</div>
                                     <div class="vhodDanie" tooltip="Температура в обратном трубопроводе">T2 <input oninput="inputCheck(this)" type="number" name="t2" value="70"  /> &#8451;</div>
                                     <div class="vhodDanie" tooltip="Давление в подающем трубопроводе">P1 <input oninput="inputCheck(this)" type="number" name="p1" />
                                        <select name="p1Type">
                                          <option>МПа</option>
                                          <option>кгс/см²</option>
                                          <option>м.в.ст.</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="Давление в обратном трубопроводе">P2 <input oninput="inputCheck(this)" type="number" name="p2" />
                                        <select name="p2Type">
                                          <option>МПа</option>
                                          <option>кгс/см²</option>
                                          <option>м.в.ст.</option>
                                        </select>
                                     </div>';

                    $modalWindow .= '</fieldset>';


                    $modalWindow .= '<fieldset class="configurBlockVvoda"><legend>Конфигурация</legend>
                                     <div class="vhodDanie labPt"><label><input id="uzel_ucheta" onchange="check(this)" type="checkbox" /> Узел учета </label></div>
                                     <div class="vhodDanie labPt"><label><input id="regul_dav_t1" onchange="check(this)" type="checkbox" /> Регулятор давления на T1 </label></div>
                                     <div class="vhodDanie labPt"><label><input id="regul_dav_t2" onchange="check(this)" type="checkbox" /> Регулятор давления на T2 </label></div>
                                     <div class="vhodDanie labPt"><label><input id="bal_klapan" onchange="check(this)" type="checkbox" /> Балансировочный клапан </label></div>';

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

                        $modalWindow .= '<div class="vhodDanie" tooltip="Нагрузка">Q <input type="number" oninput="inputCheck(this)" name="q" />
                                        <select name="qType" disabled>
                                          <option>Гкал/ч</option>
                                          <option>Ккал/ч</option>
                                          <option>МВт</option>
                                          <option>Вт</option>
                                        </select>
                                     </div>
                                   
                               
                                      <div class="vhodDanie" tooltip="Температура в подающем трубопроводе">T1 <input type="number" oninput="inputCheck(this)" name="t1" value="150" disabled="disabled" /> &#8451;</div>
                                     <div class="vhodDanie" tooltip="Температура в обратном трубопроводе">T2 <input type="number" oninput="inputCheck(this)" name="t2" value="70" disabled="disabled" /> &#8451;</div>
                                     <div class="vhodDanie" tooltip="Давление в подающем трубопроводе">P1 <input type="number" oninput="inputCheck(this)" name="p1" disabled="disabled" />
                                        <select name="p1Type" disabled>
                                          <option>МПа</option>
                                          <option>кгс/см²</option>
                                          <option>м.в.ст.</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="Давление в обратном трубопроводе">P2 <input type="number" oninput="inputCheck(this); prNasos(this)" name="p2" disabled="disabled" />
                                        <select name="p2Type" disabled>
                                          <option>МПа</option>
                                          <option>кгс/см²</option>
                                          <option>м.в.ст.</option>
                                        </select>
                                     </div>
                                     <div id="t11" class="vhodDanie" tooltip="Температура в подающем трубопроводе отопления">T11 
                                     <input type="number" oninput="inputCheck(this);" name="t11" value="90" />
                                     <!--<select name="t11" onchange="selT11(this);">
                                          <option>95</option>
                                          <option>90</option>
                                          <option>85</option>
                                          <option>80</option>
                                      </select> --> &#8451</div>
                                     <div id="t21" class="vhodDanie" tooltip="Температура в обратном трубопроводе отопления">T21 
                                     <input type="number" oninput="inputCheck(this);" name="t21" value="65" />
                                     <!--<select name="t21" onchange="selT21(this)">
                                          <option>70</option>
                                          <option>65</option>
                                          <option>60</option>
                                      </select> --> &#8451</div>
                                     <div id="dp" class="vhodDanie" tooltip="Потери давления в системе отопления">&#916;P 
                                     <input type="number" oninput="inputCheck(this)" name="dp" />
                                        <select name="dpType">
                                          <option>м.в.ст</option>
                                          <option>кПа</option>
                                          <option>Па</option>
                                        </select>
                                     </div>
                                     <div id="hVs" class="vhodDanie" tooltip="Высота здания">H <input type="number" oninput="inputCheck(this); prNasos(this)" name="h" /> м</div>
                                     ';
									 
                        $modalWindow .= '</fieldset>';
									  

                    }


                    if ($v['type'] == 'block_otopleniya') {


						  $modalWindow .= '<fieldset class="configurBlockVvoda"><legend>Конфигурация</legend>   
                                     <div class="vhodDanie" tooltip="Преобразователь частоты"><div class="pch"><span>Насос</span> <label><input id="" onchange="" name="pch" value="1" type="radio" checked /> с ПЧ</label> <label><input id="" onchange="" name="pch" value="0" type="radio" /> без ПЧ </label></div></div>
                                     <div class="vhodDanie" tooltip="" flow="right">Контроллер
                                        <select name="kontr" disabled="disabled">
                                          <option>Danfoss</option>
                                          <option selected>Segnetics</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="" flow="right">Регулирующий клапан
                                        <select name="regKlapan" disabled="disabled">
                                          <option>Danfoss</option>
                                          <option>IMI Hydronic</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="">Шкаф автоматики 
                                        <select name="shkafAvtomatiky" disabled="disabled">
                                          <option>На раме</option>
                                          <option selected>На стене</option>
                                          <option>Учтен в БОВ-1</option>
                                        </select>
                                     </div>
                                     <input class="bvNasos" type="hidden" value="0">
                                     <br>
                                     <div class="vhodDanie" tooltip="">Тип схемы
                                        <select id="bov_type_sel" name="bov_type" onchange="check(this)">
                                          <option value="1">Независимая</option>
                                          <option value="2">Зависимая</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="" flow="right">Насос
                                        <select id="nasos_type_sel" name="nasos_type" onchange="check(this)">
                                          <option value="2">Сдвоенный</option>
                                          <option value="3">Два раздельных</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="" flow="right">Расположение насоса
                                        <select id="ras_nasos_sel" name="ras_nasos" onchange="check(this)">
                                          <option value="1">Подающий трубопровод</option>
                                          <option value="2" selected>Обратный трубопровод</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="">Кол-ва теплообменников 
                                        <select id="teplo_sel" name="teplo" onchange="check(this)">
                                          <option value="1">1</option>
                                          <option value="2">2</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="">Распределение нагрузки 
                                        <select id="teploPr_sel" name="teploPr" onchange="check(this)">
                                          <option value="1">100%</option>
                                          <option value="2">75%</option>
                                          <option value="2">50%</option>
                                        </select>
                                     </div>
									                   <div class="vhodDanie" tooltip="">Регулятор температуры
                                        <select id="reg_t_sel" name="reg_t" onchange="check(this)">
                                          <option value="1">Подающий трубопровод</option>
                                          <option value="2">Обратный трубопровод</option>
                                        </select>
                                     </div>
                                     ';

                        $modalWindow .= '</fieldset>';
						
                        // $modalWindow .= '<fieldset class="configurBlockVvoda"><legend>Конфигурация</legend>   
                        //               <div class="vhodDanie" tooltip="">Тип схемы
                        //                 <select id="bov_type_sel" name="bov_type" onchange="check(this)">
                        //                   <option value="1">Независимая</option>
                        //                   <option value="2">Зависимая</option>
                        //                 </select>
                        //              </div>
                        //              <div class="vhodDanie" tooltip="" flow="right">Насос
                        //                 <select id="nasos_type_sel" name="nasos_type" onchange="check(this)">
                        //                   <option value="2">Сдвоенный</option>
                        //                   <option value="3">Два раздельных</option>
                        //                 </select>
                        //              </div>
                        //              <div class="vhodDanie" tooltip="" flow="right">Расположение насоса
                        //                 <select id="ras_nasos_sel" name="ras_nasos" onchange="check(this)">
                        //                   <option value="1">Подающий трубопровод</option>
                        //                   <option value="2">Обратный трубопровод</option>
                        //                 </select>
                        //              </div>
                        //              <div class="vhodDanie" tooltip="">Кол-ва теплообменников 
                        //                 <select id="teplo_sel" name="teplo" onchange="check(this)">
                        //                   <option value="1">1</option>
                        //                   <option value="2">2</option>
                        //                 </select>
                        //              </div>
									      //              <div class="vhodDanie" tooltip="">Регулятор температуры
                        //                 <select id="reg_t_sel" name="reg_t" onchange="check(this)">
                        //                   <option value="1">Подающий трубопровод</option>
                        //                   <option value="2">Обратный трубопровод</option>
                        //                 </select>
                        //              </div>
                        //              ';

                        // $modalWindow .= '</fieldset>';

                    }

                    if ($v['type'] == 'block_podpitky') {


                        $modalWindow .= '<fieldset class="configurBlockVvoda"><legend>Конфигурация</legend>   
                                         <div class="vhodDanie labPt" tooltip="Насосная станция">
                                                            <label style="display: none;" class="labPd"><input id="" onchange="blPodpitSogSp(this,2)" value="2" name="nasosStan" type="radio" checked/> Согласно СП </label>
                                                            <label class="labPd"><input id="" onchange="blPodpitSogSp(this,1)" value="1" name="nasosStan" type="radio" /> Да </label>
                                                            <label><input id="" onchange="blPodpitSogSp(this,0)" value="0" name="nasosStan" type="radio" /> Нет </label>
                                                            <!-- <label><input id="" onchange="blPodpitSogSp(this,3)" value="3" name="nasosStan" type="radio" /> Учтено </label> --></div>
                                         <div class="vhodDanie" tooltip="" flow="right">Повысительный насос
                                            <select name="povNasos" disabled="disabled">
                                              <option>MHI</option>
                                            </select>
                                         </div>
                                         <div class="vhodDanie" tooltip="" flow="right">Клапан соленоидный
                                            <select name="klapanSol" disabled="disabled">
                                              <option>Danfoss</option>
                                            </select>
                                         </div>
                                         <div class="vhodDanie" tooltip="" flow="right">Расширительный бак
                                            <select name="rasshirBak" disabled="disabled">
                                              <option>Wester</option>
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
                                     <div id="rd1" class="vhodDanie" tooltip="Нагрузка">Q <input type="number" oninput="inputCheck(this); qgvs(this)" name="qGVS" />
                                        <select name="qSelGVS">
                                          <option>Гкал/ч</option>
                                          <option>Ккал/ч</option>
                                          <option>МВт</option>
                                          <option>Вт</option>
                                        </select>
                                     </div>
                                     <div id="rd2" class="vhodDanie" tooltip="Расход горячей воды">Gтз <input type="number" oninput="inputCheck(this)" name="GVSgt1" /> м&#179;/ч</div>
                                     <div class="vhodDanie" tooltip="Температура в подающем трубопроводе в летний период">T1<span class="gvsLeto">л</span> <input type="number" oninput="inputCheck(this)" name="GVSt1" value="70" disabled="disabled" /> &#8451</div>
                                     <div class="vhodDanie" tooltip="Температура в обратном трубопроводе в летний период">T2<span class="gvsLeto">л</span> <input type="number" oninput="inputCheck(this)" name="GVSt2" value="30" disabled="disabled" /> &#8451;</div>
                                     <div class="vhodDanie" tooltip="Давление в подающем трубопроводе">P1 
                                     <input type="number" oninput="inputCheck(this)" name="p1" disabled="disabled" />
                                        <select name="p1Type" disabled="disabled">
                                          <option>МПа</option>
                                          <option>кгс/см²</option>
                                          <option>м.в.ст.</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="Давление в обратном трубопроводе">P2 
                                     <input type="number" oninput="inputCheck(this)" name="p2" disabled="disabled" />
                                        <select name="p2Type" disabled="disabled">
                                          <option>МПа</option>
                                          <option>кгс/см²</option>
                                          <option>м.в.ст.</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="Температура горячей воды">T3 <input type="number" oninput="inputCheck(this)" value="65" name="GVSt3" value="65" disabled="disabled" /> &#8451</div>
                                     <div class="vhodDanie" tooltip="Температура холодной воды">Tв1 <input type="number" oninput="inputCheck(this)" value="5" name="GVStv1" value="5" disabled="disabled" /> &#8451</div>
                                     <div class="vhodDanie" tooltip="Давление в трубопроводе холодной воды">Pв1 <input type="number" oninput="inputCheck(this)" name="pb1" />
                                        <select name="pb1SelGVS" disabled="disabled">
                                          <option>МПа</option>
                                          <option>кгс/см²</option>
                                          <option>м.в.ст.</option>
                                        </select>
                                     </div>
                                     ';

                    $modalWindow .= '</fieldset>';


                    $modalWindow .= '<fieldset><legend>Конфигурация</legend>';

                    $modalWindow .= '<div class="vhodDanie labPt" tooltip="Схема подключения" flow="right" style="display: none;">
                                                            <label class="labPd"><input id="" onchange="shPodkl(this)" value="1" name="shemaPodkl" type="radio" checked /> Согласно СП </label>
                                                            <label class="labPd"><input id="" onchange="shPodkl(this)" value="2" name="shemaPodkl" type="radio" /> Параллельная </label>
                                                            <label><input id="" onchange="shPodkl(this)" value="3" name="shemaPodkl" type="radio" /> Смешанная </label></div>
                                     <div class="vhodDanie" tooltip="" flow="right">Контроллер
                                        <select name="kontrGVS" disabled="disabled">
                                          <option>Danfoss</option>
                                          <option selected>Segnetics</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="" flow="right">Регулирующий клапан
                                        <select name="regKlapanGVS" disabled="disabled">
                                          <option>Danfoss</option>
                                          <option>IMI Hydronic</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="">Шкаф автоматики 
                                        <select name="shkafAvtomatikyGVS" disabled="disabled">
                                          <option>На раме</option>
                                          <option selected>На стене</option>
                                          <option>Учтен в БОВ-1</option>
                                        </select>
                                     </div>
                                     <br>

                                     <div class="vhodDanie" tooltip="">Тип схемы
                                        <select id="gvs_type_sel" name="gvs_type" onchange="check(this); change_gvs_type_sel_gvs(this);">
                                          <option value="1">Смешанная</option>
                                          <option value="2">Параллельная</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="" flow="right">Насос
                                        <select id="nasos_type_sel" name="nasos_type" onchange="check(this)">
										                      <option value="1">Одинарный</option>
                                          <!--<option value="2">Сдвоенный</option>-->
                                          <option value="3">Два раздельных</option>
                                        </select>
                                     </div>                                    
                                     <div class="vhodDanie" tooltip="">Кол-ва теплообменников 
                                        <select id="teplo_sel" name="teplo" onchange="check(this)">
                                          <option value="1">1</option>
                                          <option value="2">2</option>
                                        </select>
                                     </div>
                                     <div class="vhodDanie" tooltip="">Распределение нагрузки 
                                     <select id="teploPr_sel" name="teploPr" onchange="check(this)">
                                       <option value="1">100%</option>
                                       <option value="2">75%</option>
                                       <option value="2">50%</option>
                                     </select>
                                  </div>
									                   <div class="vhodDanie" tooltip="">Регулятор температуры
                                        <select id="reg_t_sel" name="reg_t" onchange="check(this)" disabled>
                                          <option value="1">Подающий трубопровод</option>
                                          <option value="2">Обратный трубопровод</option>
                                        </select>
                                     </div>
                                     
                                     ';


                    $modalWindow .= '</fieldset>';
					
					//  $modalWindow .= '<fieldset class="configurBlockVvoda"><legend>Конфигурация</legend>   
          //                            <div class="vhodDanie" tooltip="">Тип схемы
          //                               <select id="gvs_type_sel" name="gvs_type" onchange="check(this)">
          //                                 <option value="1">Смешанная</option>
          //                                 <option value="2">Параллельная</option>
          //                               </select>
          //                            </div>
          //                            <div class="vhodDanie" tooltip="" flow="right">Насос
          //                               <select id="nasos_type_sel" name="nasos_type" onchange="check(this)">
					// 					  <option value="1">Одинарный</option>
          //                                 <option value="2">Сдвоенный</option>
          //                                 <option value="3">Два раздельных</option>
          //                               </select>
          //                            </div>                                    
          //                            <div class="vhodDanie" tooltip="">Кол-ва теплообменников 
          //                               <select id="teplo_sel" name="teplo" onchange="check(this)">
          //                                 <option value="1">1</option>
          //                                 <option value="2">2</option>
          //                               </select>
          //                            </div>
					// 				  <div class="vhodDanie" tooltip="">Регулятор температуры
          //                               <select id="reg_t_sel" name="reg_t" onchange="check(this)">
          //                                 <option value="1">Подающий трубопровод</option>
          //                                 <option value="2">Обратный трубопровод</option>
          //                               </select>
          //                            </div>
          //                            ';


          //               $modalWindow .= '</fieldset>';


                }


                // Узел учета
                // Регулятор перепада
                // Балансировочный клапан
                //

                // Кнопки модальных окон

                $modalWindow .= '<div class="blockPsCarusel"><div class="carouselPrev"><i class="fa fa-chevron-left" onclick="caruselPrevNext(this, \'prev\')" aria-hidden="true"></i></div><div class="carouselNext"><i class="fa fa-chevron-right" onclick="caruselPrevNext(this, \'next\')" aria-hidden="true"></i></div><div class="carousel"><div class="carousel-dv">';
                    
                foreach ($data as $vCarusel) {


                    if ($v['type'] == $vCarusel['type']) {
					$widthImg = '650px';
						if($vCarusel['type'] == 'block_vvoda') {
							$widthImg = '450px';
						}
                        $modalWindow .= '<div onclick="caruselClickActive(this)" class="carousel-element" data-bal_klapan="' . $vCarusel['bal_klapan'] . '" data-regul_dav_t1="' . $vCarusel['regul_dav_t1'] . '" data-regul_dav_t2="' . $vCarusel['regul_dav_t2'] . '" data-uzel_ucheta="' . $vCarusel['uzel_ucheta'] . '" data-nasos_type="' . $vCarusel['nasos_type'] . '" data-ras_nasos="' . $vCarusel['ras_nasos'] . '" data-teplo="' . $vCarusel['teplo'] . '" data-reg_t="' . $vCarusel['reg_t'] . '" data-bov_type="' . $vCarusel['bov_type'] . '" data-gvs_type="' . $vCarusel['gvs_type'] . '" data-line="' . $vCarusel['line'] . '" data-nasos="' . $vCarusel['nasos'] . '" data-id="' . $vCarusel['id'] . '"><img style="width: '.$widthImg.'" class="blockImg" src="'. THEME_CONSTRUCT.'images/' . $vCarusel['type'] . '/' . $vCarusel['image_block'] . '" data-left="' . $vCarusel['left'] . '" data-t="' . $vCarusel['t'] . '" data-podpitka="' . $vCarusel['podpitka'] . '" data-podpitka-h="' . $vCarusel['podpitka_h'] . '" data-line="' . $vCarusel['line'] . '" data-id="' . $vCarusel['id'] . '" data-type="' . $vCarusel['type'] . '" data-image="'. THEME_CONSTRUCT.'images/'. $vCarusel['type'] . '/' . $vCarusel['image_block'] . '" data-h1="' . $vCarusel['h1'] . '" data-h2="' . $vCarusel['h2'] . '" data-c1="' . $vCarusel['c1'] . '" data-c2="' . $vCarusel['c2'] . '" alt=""/></div>';

                    }


                }

                $modalWindow .= '</div></div></div></div>';
                
                $modalWindow .= '<div class="modalMessage"><label>Комментарий:</label><textarea name="message"></textarea></div>';

                ////////////////
                // Данные шкафов
                ////////////////

                if ($v['type'] == 'block_vvoda') {


                    $modalWindow .= '<fieldset class="configurBlockVvoda blockVvodaTeplovich"><legend>Тепловычислитель</legend>
                                         <div>
                                         <div class="vhodDanie labPt"><label><input id="shkafPodpitka" onchange="" type="checkbox" /> Подпиточный трубопровод в отдельной теплосистеме </label></div>
                                         </div><div>
                                         <div class="vhodDanie labPt"><label><input id="regul_dav_t2" onchange="shkafDpInter(this)" type="checkbox" /> Дополнительный интерфейс </label></div>
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