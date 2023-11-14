<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Test class
*/

namespace Project\Controller;

use \Boot\Controller\AbstractController;
use DateTime;


class PublicMeteringController extends AbstractController
{


    public static function test()
    {

        $get = $_GET;
        
        $dir = dirname(__FILE__);

        

        echo '<form>

            <h3>Рабочая документация</h3>

            <p>Удмуртская Республика ООО «Атри-Энерго» Свидетельство СРО-П-215-18102019 от 28.01.2020 Ассоциация «СФЕРА проектировщиков» <input type="text" name="UR" placeholder="" /><br></p>
            <p>Главный инженер проекта                                 _________ А.Ю. Ложкин <input type="text" name="gl_inzhener" placeholder="" /></p>
            <p>Представитель ООО «УКС» и ПАО «Т Плюс» подоверенности <input type="text" name="pred" placeholder="" /><br></p>
            <p>_________  А.Р. Каюмов <input type="text" name="pred_FIO" placeholder="" /><br</p>
            <p>«___»___________2021г. <input type="text" name="pred_god" placeholder="" /><br</p>
            <p>_________  Л.А. Ловкин <input type="text" name="pred_FIO1" placeholder="" /><b</p>
            <p>Ижевск, 2021 <input type="text" name="izh2021" placeholder="" /><br></p>

            <h3>Первая страница</h3>

            <p>Узел учёта тепла (АП-У-555/21-ТМ) <input type="text" name="pr_numb1" placeholder="" /><br></p>
            <p>Автоматизация узла учета тепла (АП-У-555/21-ATM) <input type="text" name="pr_numb2" placeholder="" /><br></p>
            <p>Спецификация оборудования и материалов (АП-У-555/21-TM.C) <input type="text" name="pr_numb3" placeholder="" /><br></p>
            <p>АП-У-555/21-АТМ.С <input type="text" name="pr_numb4" placeholder="" /><br></p>
            <p>9 АП-У-555/21-ATM <input type="text" name="pr_numb5" placeholder="" /><br></p>
            <p>Разработал: <input type="text" name="raz" placeholder="" /><br></p>
            <p>Проверил: <input type="text" name="prov" placeholder="" /><br></p>
            <p>ГИП: <input type="text" name="gip" placeholder="" /><br></p>
            <p>Н.Контроль: <input type="text" name="control" placeholder="" /><br></p>
            <p>Дата проекта: <input type="text" name="date" placeholder="" /><br></p>
            <p>г.Ижевск, ул.Салютовская, 5а <input type="text" name="address" placeholder="" /><br></p>
            <p>ООО \"Атри-Энерго\" г.Ижевск <input type="text" name="company_and_сity" placeholder="" /><br></p>

            <h3>Пояснительная записка</h3>
            <p>Условий подключений к системе теплоснабжения, выданных ООО \"УКС\". <input type="text" name="punkt1" placeholder="" /><br></p>
            <p>независимая <input type="text" name="sys_otopleniya" placeholder="" /><br></p>
            <p>закрытая/2-ступенчатая смешанная <input type="text" name="sys_GVS" placeholder="" /><br></p>

            <p>150-70 <input type="text" name="GOT" placeholder="" /><br></p>
            <p>1,18 <input type="text" name="davlenie_v_pod" placeholder="" /><br></p>
            <p>0,18 <input type="text" name="davlenie_v_obr" placeholder="" /><br></p>
            <p>"ATM" <input type="text" name="punkt3" placeholder="" /><br></p>
            

            <button type="submit">Не нажимай!</button>
        
        </form>';

        //Проверяем есть ли в списке слово UR 

        if (isset($get['UR'])) 
        {
            // echo $get['UR'];
            $req = [

                //Рабочая документация
                'org_region' => $get['org_region'] ?? '',
                'org_name' => $get['org_name'] ?? '',
                'sro_numb' => $get['sro_numb'] ?? '',
                'sro_name' => $get['sro_name'] ?? '',


                'object_full' => $get['object_full'] ?? '',
                'pr_numb' => $get['pr_numb'] ?? '',
                'tso_title' => $get['tso_title'] ?? '',
                'tso_prov_1' => $get['tso_prov_1'] ?? '',
                'tso_prov_2' => $get['tso_prov_2'] ?? '',
                'created_at_year' => $get['created_at_year'] ?? '',
                'org_city' => $get['org_city'] ?? '',
                


                'gl_inzhener' => $get['gl_inzhener'] ?? '',
                'pred' => $get['pred'] ?? '',
                'pred_FIO' => $get['pred_FIO'] ?? '',
                'pred_god' => $get['pred_god'] ?? '',
                'pred_FIO1' => $get['pred_FIO1'] ?? '',
                

                //Первая страница
                'pr_numb1' => $get['pr_numb1'] ?? '',
                'pr_numb2' => $get['pr_numb2'] ?? '',
                'pr_numb3' => $get['pr_numb3'] ?? '',
                'raz' => $get['raz'] ?? '',
                'prov' => $get['prov'] ?? '',
                'gip' => $get['gip'] ?? '',
                'control' => $get['control'] ?? '',
                'date' => $get['date'] ?? '',
                'address' => $get['address'] ?? '',
                'company_and_city' => $get['company_and_city'] ?? '', 

                //Пояснительная записка
                'punkt1' => $get['punkt1'] ?? '',
                'sys_otopleniya' => $get['sys_otopleniya'] ?? '',
                'sys_GVS' => $get['sys_GVS'] ?? '',
                'GOT' => $get['GOT'] ?? '',
                'davlenie_v_pod' => $get['davlenie_v_pod'] ?? '',
                'davlenie_v_obr' => $get['davlenie_v_obr'] ?? '',
                'punkt3' => $get['punkt3'] ?? '',

                //Выбор учета приборов
                'ot_ras_e' => $get['ot_ras_e'] ?? '',
                'GVS2_ras_e' => $get['GVS2_ras_e'] ?? '',
                'GVS1_ras_e' => $get['GVS1_ras_e'] ?? '',
                'it_ras_e' => $get['it_ras_e'] ?? '',
                'ot_temp' => $get['ot_temp'] ?? '',
                'GVS2_temp' => $get['GVS2_temp'] ?? '',
                'GVS1_1_temp' => $get['GVS1_1_temp'] ?? '',
                'GVS1_2_temp' => $get['GVS1_2_temp'] ?? '',
                'ot_ras_v' => $get['ot_ras_v'] ?? '',
                'GVS2_ras_v' => $get['GVS2_ras_v'] ?? '',
                'GVS1_ras_v' => $get['GVS1_ras_v'] ?? '',
                'it_ras_v' => $get['it_ras_v'] ?? '',
                'podpitka' => $get['podpitka'] ?? '',
                'ras_napor' => $get['ras_napor'] ?? '',
                'TR_dat' => $get['TR_dat'] ?? '',
                'piterF_1' => $get['piterF_1'] ?? '',
                'piterF_2' => $get['piterF_2'] ?? '',
                'diapazon_izmer_1' => $get['diapazon_izmer_1'] ?? '',
                'diapazon_izmer_1.1' => $get['diapazon_izmer_1.1'] ?? '',
                'diapazon_izmer_2' => $get['diapazon_izmer_2'] ?? '',
                'snizh_nap_1' => $get['snizh_nap_1'] ?? '',
                'snizh_nap_2' => $get['snizh_nap_2'] ?? '',
                'sis_otop1' => $get['sis_otop1'] ?? '',
                'sis_otop2' => $get['sis_otop2'] ?? '',

                //Узел учёт ?? ''а
                'iz_teplo_t1' => $get['iz_teplo_t1'] ?? '',
                'v_teploset_t2' => $get['v_teploset_t2'] ?? '',
                'k_potreb_t1' => $get['k_potreb_t1'] ?? '',
                'ot_potreb_t2' => $get['ot_potreb_t2'] ?? '',
                'k_potrebt_t94' => $get['k_potrebt_t94'] ?? '',
                'K_SH7' => $get['K_SH7'] ?? '',
                'K_SH8' => $get['K_SH8'] ?? '',
                'ras_2' => $get['ras_2'] ?? '',
                'ras_3' => $get['ras_3'] ?? '',
                'TE' => $get['TE'] ?? '',
                'PE1' => $get['PE1'] ?? '',
                'PE2' => $get['PE2'] ?? '',

                //Способ монтажа датчиков температуры на Т1, Т2
                '15.5' => $get['15.5'] ?? '',
                '100' => $get['100'] ?? '',
                '33' => $get['33'] ?? '',
                'ob1' => $get['ob1'] ?? '',
                'pt100' => $get['pt100'] ?? '',

                //Способ монтажа датчиков давления на Т1, Т2
                'ob3.1' => $get['ob3.1'] ?? '',
                'name3.1' => $get['name3.1'] ?? '',
                'name3.2' => $get['name3.2'] ?? '',
                'null' => $get['null'] ?? '',

                //Схема функциональная автоматизации узла учета тепловой энергии и теплоносителя
                'PE1.2' => $get['PE1.2'] ?? '',
                'PE1.5' => $get['PE1.5'] ?? '',
                'TE1.1' => $get['TE1.1'] ?? '',
                'TE1.4' => $get['TE1.4'] ?? '',
                'FE1.3' => $get['FE1.3'] ?? '',
                'FE1.6' => $get['FE1.6'] ?? '',
                'FE1.7' => $get['FE1.7'] ?? '',
                '1a' => $get['1a'] ?? '',
                '1' => $get['1'] ?? '',
                '2' => $get['2'] ?? '',
                '3' => $get['3'] ?? '',
                '4' => $get['4'] ?? '',
                '5' => $get['5'] ?? '',
                '6' => $get['6'] ?? '',
                '7' => $get['7'] ?? '',
                '8' => $get['8'] ?? '',
                '9' => $get['9'] ?? '',
                'list6' => $get['list6'] ?? '',
                'list7' => $get['list7'] ?? '',
                'list9' => $get['list9'] ?? '',
                'list10' => $get['list10'] ?? '',

                //Общие указани я
                'teplov' => $get['teplov'] ?? '',
                'PR' => $get['PR'] ?? '',
                'termiko' => $get['termiko'] ?? '',
                'pr_dav' => $get['pr_dav'] ?? '',

                //# Труба 1
                'ves_IMP' => $get['ves_IMP'] ?? '',
                'Vmax1' => $get['Vmax1'] ?? '',
                'Vmin1' => $get['Vmin1'] ?? '',
                'Rdog1' => $get['Rdog1'] ?? '',
                'Rv1' => $get['Rv1'] ?? '',

                //Труба 2
                'Vmax2' => $get['Vmax2'] ?? '',
                'Vmin2' => $get['Vmin2'] ?? '',
                'Rdog2' => $get['Rdog2'] ?? '',
                'Rv2' => $get['Rv2'] ?? '',

                //Труба 1.1
                'ves_IMP3' => $get['ves_IMP3'] ?? '',
                'Vmax3' => $get['Vmax3'] ?? '',
                'Vmin3' => $get['Vmin3'] ?? '',
                'Rdog3' => $get['Rdog3'] ?? '',


            ];
            
            $req = json_encode($req, JSON_UNESCAPED_UNICODE);
            dump($req);
            exec("python3 {$dir}/1.py 2>&1 '{$req}' ", $output);
            dump($output);
        }

        echo '<a href="https://atri-energo.ru/usr/modules/project/Controller/" id="nameDXF">Скачать</a>';

        //exec('python3' . $dir . '/qqq.py', $qwe);

        // exec('python3 test.py "in/example.pdf" "1 2 3"', $output);
        //var_dump($output);

    }
}
