<!doctype html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Atri-Energo</title>
    <link rel="stylesheet" media="all" href="<?Php echo THEME_CONSTRUCT;?>/css/style.css"/>
</head>

<body>

<header>
    <div id="logo"><img src="<?php echo THEME_CONSTRUCT;?>/images/logo-default.png" alt=""/></div>
</header>


<div id="blockLeft">

    <ul class="l-menu">
    <li><a href="javascript:void(0)" onclick="shemaDoc(1)"><i class="fa fa-sitemap"></i> Схемы управления</a></li>
    <li><a href="javascript:void(0)" onclick="shemaDoc(2)"><i class="fa fa-file-text"></i> Исполнительная документация</a></li>
    <li><a href="javascript:void(0)" onclick="shemaDoc(3)"><i class="fa fa-asterisk"></i> Съем показаний</a></li>
    <li><a href="javascript:void(0)" onclick="shemaDoc(4)"><i class="fa fa-book"></i> Журналы, отчеты</a></li>
    <li><a href="javascript:void(0)" onclick="shemaDoc(5)"><i class="fa fa-cogs"></i> Оборудование</a></li>
    </ul>

</div>
<div id="blockRight">
    <div id="shemaUpravleniya">
        <div id="shema">

        </div>
        <div id="map-shema">
            <div class="nasos1">
                <label><input type="radio" name="nasos1" checked="checked" /> Откр.</label>
                <label><input type="radio" name="nasos1" /> Закр.</label>
            </div>
            <div class="nasos2">
                <label><input type="radio" name="nasos2" checked="checked" /> Откр.</label>
                <label><input type="radio" name="nasos2" /> Закр.</label>
            </div>
            <div class="nasos3">
                <label><input type="radio" name="nasos3" checked="checked" /> Руч.</label>
                <label><input type="radio" name="nasos3" /> Авто</label>
            </div>
            <div class="nasos4">
                <label><input type="radio" name="nasos4" checked="checked" /> Руч.</label>
                <label><input type="radio" name="nasos4" /> Авто</label>
            </div>
            <div class="nasos5">
                <label><input type="checkbox" onchange="vint1(this)" name="nasos5" /> Вкл./Выкл.</label>
            </div>
            <div class="nasos6">
                <label><input type="checkbox" onchange="vint2(this)" name="nasos6" /> Вкл./Выкл.</label>
            </div>
            <div class="vint1"></div>
            <div class="vint2"></div>
            <div class="vint3"></div>
            <div class="vint4"></div>
        </div>
    </div>
    <div id="ispolDoc">
        <h2>Исполнительная документация</h2>
        <div class="isDoc isDocActive"><a href="https://atri-energo.ru/doc/last"><i class="fa fa-file-text" aria-hidden="true"></i> <span>Акт о проведении гидравлических испытаний</a></span></div>
        <div class="isDoc"><i class="fa fa-file-text" aria-hidden="true"></i> <span>Акт о приемке помещения к монтажу</span></div>
        <div class="isDoc"><i class="fa fa-file-text" aria-hidden="true"></i> <span>Акт о приемке группы учета электроэнергии</span></div>
        <div class="isDoc"><i class="fa fa-file-text" aria-hidden="true"></i> <span>Журнал производства работ</span></div>
        <div class="isDoc"><i class="fa fa-file-text" aria-hidden="true"></i> <span>Акт об окончании монтажа оборудования</span></div>
        <div class="isDoc"><i class="fa fa-file-text" aria-hidden="true"></i> <span>Акт о проведении промывки системы</span></div>
        <div class="isDoc"><i class="fa fa-file-text" aria-hidden="true"></i> <span>Акт скрытых работ по огрунтовке/покраске трубопроводов</span></div>
        <div class="isDoc"><i class="fa fa-file-text" aria-hidden="true"></i> <span>Акт на монтаж изоляции</span></div>
        <div class="isDoc"><i class="fa fa-file-text" aria-hidden="true"></i> <span>Акт об окончании работ по автоматизации</span></div>
        <div class="isDoc"><i class="fa fa-file-text" aria-hidden="true"></i> <span>Акт приема-передачи оборудования и помещения</span></div>
        <div class="isDoc"><i class="fa fa-file-text" aria-hidden="true"></i> <span>Акт о проведении пуско-наладочных работ</span></div>
        <div class="isDoc"><i class="fa fa-file-text" aria-hidden="true"></i> <span>Акт комплексного опробования систем</span></div>
        <div class="isDoc"><i class="fa fa-file-text" aria-hidden="true"></i> <span>Договор на проведение пуско-наладочных работ</span></div>
        <div class="isDoc"><i class="fa fa-file-text" aria-hidden="true"></i> <span>Договор на обслуживание</span></div>
    </div>
	<div id="b1">
		<img src="<?php echo THEME_CONSTRUCT;?>/images/45.gif" alt=""/>
	</div>
	<div id="b2">
		<img src="<?php echo THEME_CONSTRUCT;?>/images/007.png" alt=""/>
		<h3>Произошла ошибка загрузки журналов и отчетов!</h3>
	</div>
	<div id="b3">
		<table class="table table-striped" id="tablesForm">
                <thead>
                <tr>
                <th class="w40"><span class="pd-l-sm"></span>№</th>
                <th style="width: 200px;">Номенклатура</th>
                <th style="width: 70px;">Артикул</th>
                <th style="width: 70px;" class="w50 text-center">Кол-во</th>	
                <th class="w150">Цена</th>
                <th class="w200">Сумма</th>
                <th class="w150">Наличие</th>	
                <th class="w150">Дата поставки</th>							
                </tr>
                </thead>
                <tbody id="tov_list"><tr><td data-toggle="tooltip" title="39984"><span class="pd-l-sm"></span>1</td>
                    <td class="w100"><a href="/catalog_v2/view/39990" target="_blank">Блок ввода - 2-09-4-5-1-07.2-06.1</a></td>
                    <td class="w50">AE05407039990</td>
                    <td class="text-center">1</td>											
                    <td>248 649.30 руб.</td>		
                    <td>248 649.30 руб.</td>
                    <td><font color="red">Нет</font></td>
                    <td>По запросу</td></tr><tr><td data-toggle="tooltip" title="39985"><span class="pd-l-sm"></span>2</td>
                    <td class="w100"><a href="/catalog_v2/view/39986" target="_blank">Блок отполения/вентиляции - БОВ-1 2-07-6.2-09-7.1-4.1-2-2</a></td>
                    <td class="w50">AE05407039986</td>
                    <td class="text-center">1</td>											
                    <td>876 941.90 руб.</td>		
                    <td>876 941.90 руб.</td>
                    <td><font color="red">Нет</font></td>
                    <td>По запросу</td></tr><tr><td data-toggle="tooltip" title="39986"><span class="pd-l-sm"></span>3</td>
                    <td class="w100"><a href="/catalog_v2/view/39989" target="_blank">Блок подпитки - 4-03.1-2.1-11.1</a></td>
                    <td class="w50">AE05407039989</td>
                    <td class="text-center">2</td>											
                    <td>314 390.69 руб.</td>		
                    <td>628 781.38 руб.</td>
                    <td><font color="red">Нет</font></td>
                    <td>По запросу</td></tr><tr><td data-toggle="tooltip" title="39987"><span class="pd-l-sm"></span>4</td>
                    <td class="w100"><a href="/catalog_v2/view/33522" target="_blank">Труба стальная электросварная прямошовная ГОСТ 10704-91 108х4</a></td>
                    <td class="w50"></td>
                    <td class="text-center">2</td>											
                    <td>753.60 руб.</td>		
                    <td>1 507.20 руб.</td>
                    <td><font color="red">Нет</font></td>
                    <td>По запросу</td></tr><tr><td data-toggle="tooltip" title="39988"><span class="pd-l-sm"></span>5</td>
                    <td class="w100"><a href="/catalog_v2/view/969" target="_blank">РИДАН-ЗДМ.01.16.050 PN16 DN50 корпус чугун; диск чугун,эпоксидное покрытие; уплотнение EPDM; рукоят</a></td>
                    <td class="w50">082X4000</td>
                    <td class="text-center">1</td>											
                    <td>3 016.96 руб.</td>		
                    <td>3 016.96 руб.</td>
                    <td><font color="green">Да</font></td>
                    <td>По запросу</td></tr><tr><td data-toggle="tooltip" title="39989"><span class="pd-l-sm"></span>6</td>
                    <td class="w100"><a href="/catalog_v2/view/832" target="_blank">Манометр 0-16 кгс/см2 кл.т.1,5 M20*1,5 ФизТех исп. ЭКО</a></td>
                    <td class="w50">AE201400004</td>
                    <td class="text-center">1</td>											
                    <td>488.14 руб.</td>		
                    <td>488.14 руб.</td>
                    <td><font color="green">Да</font></td>
                    <td>По запросу</td></tr><tr><td data-toggle="tooltip" title="39990"><span class="pd-l-sm"></span>7</td>
                    <td class="w100"><a href="/catalog_v2/view/307" target="_blank">Трубка K-FLEX 19x108-2 ST (в упаковке 12м)</a></td>
                    <td class="w50">19108005508</td>
                    <td class="text-center">1</td>											
                    <td>715.65 руб.</td>		
                    <td>715.65 руб.</td>
                    <td><font color="green">Да</font></td>
                    <td>По запросу</td></tr></tbody> 
				<tfoot>
				<tr><td colspan="5" class="text-right"><b>Итого:</b></td><td colspan="3" class="text-left">1 519 274.48 руб.</td></tr>
				<tr><td colspan="5" class="text-right" style="border-top: 0px solid #ddd;"><b>В т.ч. НДС (20%): </b></td><td style="border-top: 0px solid #ddd;" colspan="3" class="text-left">240 826.05 руб.</td></tr>
				<tr><td colspan="5" class="text-right" style="border-top: 0px solid #ddd;"><b>Итого с НДС: </b></td><td style="border-top: 0px solid #ddd;" colspan="3" class="text-left">1 760 100.53 руб.</td></tr>
				</tfoot>
				</table>
	</div>
</div>



<link rel="stylesheet" href="/usr/tpl/dev/assest/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo THEME_CONSTRUCT;?>/css/jquery.arcticmodal-0.3.css">
<link rel="stylesheet" href="<?php echo THEME_CONSTRUCT;?>/css/simple.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="<?php echo THEME_CONSTRUCT;?>/js/jquery.arcticmodal-0.3.min.js"></script>
<script src='<?php echo THEME_CONSTRUCT;?>/js/scripts.js'></script>
</body>

</html>