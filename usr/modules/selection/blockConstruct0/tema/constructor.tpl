<!doctype html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Atri-Energo</title>
    <link rel="shortcut icon" href="/usr/tpl/main/assest/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" media="all" href="<?php echo THEME_CONSTRUCT; ?>css/tooltip.css"/>
    <link rel="stylesheet" media="all" href="<?php echo THEME_CONSTRUCT; ?>css/style.css"/>
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
    <div id="logo"><img src="<?php echo THEME_CONSTRUCT; ?>images/logo-default.png" alt=""/></div>
    <button class="btn btnRed" onclick="calculateCost()"><i class="fa fa-calculator" aria-hidden="true"></i> Рассчитать стоимость</button>
    <div id="mapTrash" class="atimate" tooltip="Очистить" flow="down"><i onclick="removeMap()" class="fa fa-trash"  aria-hidden="true"></i></div>
    <div id="myCircuit" class="atimate" tooltip="Мои схемы" flow="down"><i onclick="" class="fa fa-database"  aria-hidden="true"></i></div>
    <div id="standardSolution" class="atimate" tooltip="Типовые решения" flow="down"><i onclick="" class="fa fa-sitemap"  aria-hidden="true"></i></div>
</header>


<div id="blockLeft">

    <?php foreach($data as $v){ ?>
     <? if($v['menu']){ ?>

    <div class="draggable blockLeftCont" data-type="<?=$v['type']?>">
        <h3><?=$v['name']?></h3>
        <img class="blockImg" src="<?php echo THEME_CONSTRUCT; ?>/images/model/<?=$v['image']?>" data-podpitka="<?=$v['podpitka']?>" data-t="<?=$v['t']?>" data-left="<?=$v['left']?>" data-type="<?=$v['type']?>" data-image="<?=$v['type']?>/<?=$v['image_block']?>" data-h1="<?=$v['h1']?>" data-h2="<?=$v['h2']?>" data-c1="<?=$v['c1']?>" data-c2="<?=$v['c2']?>" alt=""/>
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
                        <div id="joiningFeeder">Присоединение к подающему <span>трубопроводу по мосту</span></div>
                        <span class="thermalNetwork">Тепловая сеть</span>
                        <div id="attachReverse">Присоединение к обратному <span>трубопроводу по мосту</span></div>
                    </div>
                    <div class="heat"></div>
                    <div class="heatColdIndent"></div>
                    <div class="cold"></div>
                </div>
                <div class="MapBlockImg blockVvodaDS">
                    <div onclick="modalGenerator('block_vvoda','block_vvoda','',this)" class="MapBlockDrop MapBlockDropInput blockActiveHover">
                        <div class="insertBlockNew insertBlockNull"><div class="iconHover"></div>
                            <span>Вставить блок ввода</span>
                        </div>
                    </div>
                </div>
                <div class="MapBlockImg MapBlockDropList">
                    <div class="strongBlockTop">
                        <div class="MapBlockDrop blockOtopleniyaList blockActive" onclick="modalGenerator('block_otopleniya','','',this)">
                            <div class="insertBlockOtopleniya"><div class="iconHover"></div><span>Вставить блок отопления</span></div>
                        </div>
                        <div class="blockSystem"></div>
                    </div>
                    <div class="MapBlockDrop blockPodpitkyList blockActive" onclick="modalGenerator('block_podpitky','','',this)">
                        <div class="insertBlockPodpitky"><div class="iconHover"></div><span>Вставить блок подпитки</span></div>
                    </div>
                    <div class="strongBlock"><div class="MapBlockDrop"></div><div class="blockSystem"></div></div>
                </div>
            </div>




            <div class="clear"></div>

            <svg id="svg-line">
                <line></line>
            </svg>
            <div id="svg-point"></div>
        </div>





    </div>
</div>


<?=$modalWindow?>
<div id="systemMessage"></div>
<div id="raschetStoimosty">
    <div class="raschetStoimostyLoading">
        <div class="cssload-loader">
            <div class="cssload-inner cssload-one"></div>
            <div class="cssload-inner cssload-two"></div>
            <div class="cssload-inner cssload-three"></div>
        </div>
        <div class="loaderText">Расчет стоимости...</div>
    </div>
    <div class="raschetStoimostyResult"></div>
</div>
<span id="i-data" class="spanHidden"></span>
<span id="i-data-general" class="spanHidden"></span>
<span id="i-data-block" class="spanHidden"></span>
<span id="i-q" class="spanHidden"></span>

	<link rel="stylesheet" href="/usr/tpl/dev/assest/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo THEME_CONSTRUCT; ?>css/jquery.arcticmodal-0.3.css">
<link rel="stylesheet" href="<?php echo THEME_CONSTRUCT; ?>css/simple.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="<?php echo THEME_CONSTRUCT; ?>js/jquery-ui.min.js"></script>
<script src="<?php echo THEME_CONSTRUCT; ?>js/jquery.nicescroll.min.js"></script>
<script src="<?php echo THEME_CONSTRUCT; ?>js/jquery.arcticmodal-0.3.min.js"></script>
<script src='<?php echo THEME_CONSTRUCT; ?>js/scripts.js'></script>
</body>

</html>