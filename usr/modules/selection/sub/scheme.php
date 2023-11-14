<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

function scheme() {
	echo '
	<!DOCTYPE html>
	<html>
	<head>
    <meta charset="UTF-8">
    <title>Atri-Energo</title>
    <link rel="shortcut icon" href="/usr/tpl/main/assest/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" media="all" href="/usr/modules/selection/blockConstruct/css/tooltip.css">
    <link rel="stylesheet" media="all" href="/usr/modules/selection/blockConstruct/css/styleView.css">
</head>
<body><div id="blockRight">

    <div id="blockRightWrap" style="display: block;">

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
                    <div onclick="" class="MapBlockDrop MapBlockDropInput ui-droppable"><div class="dropBlock draggable block_vvoda"><h3 data-scid="0"><div class="divBotPosition"><i class="fa fa-pencil"></i> <i class="fa fa-trash" onclick="deleteBlock(this)"></i></div>Блок ввода</h3><img class="blockImg" src="/usr/modules/selection/blockConstruct/images/block_vvoda/28.svg" data-t="177" data-podpitka="1" data-podpitka-h="0" data-left="22" data-lile="1" data-type="block_vvoda" data-id="45" style="width: 405px" data-h1="29" data-h2="29" data-c1="91" data-c2="91" alt=""><span class="spanHidden" data-type="block_vvoda">{"block_vvoda": {"q": "0","id": "45","qType": "Гкал/ч","uzel_ucheta": "true","regul_dav_t1": "true","regul_dav_t2": "true","bal_klapan": "true","shkafPodpitka": "false","shkafDopObor": "false","skafDi": "1","message": "","skafDo": "1"}}</span></div></div>
                </div>
                <div class="MapBlockImg MapBlockDropList">

                    <div class="strongBlock">
                        <div class="MapBlockDrop mmc ui-droppable"><div onclick="" class="dropBlock draggable block_otopleniya"><h3 data-scid="1"><div class="divBotPosition"><i class="fa fa-pencil"></i> <i class="fa fa-trash" onclick="deleteBlock(this)"></i></div>Блок отопления</h3><img class="blockImg" src="/usr/modules/selection/blockConstruct/images/block_otopleniya/1.svg" data-t="0" data-podpitka="392" data-podpitka-h="11" data-left="0" data-lile="1" data-type="block_otopleniya" data-id="50" style="width: 550px" data-h1="29" data-h2="0" data-c1="91" data-c2="91" alt=""><span class="spanHidden" data-type="block_otopleniya">{"block_otopleniya": {"q": "0.7","id": "50","qType": "Гкал/ч","kontr": "Segnetics","regKlapan": "Danfoss","t11": "90","t21": "65","dpType": "м.в.ст","shkafAvtomatiky": "На стене","typeBlock": "1","pch": "1","dp": "5","bvNasos": "1","bov_type": "Независимая","nasos_type": "Сдвоенный","ras_nasos": "Обратный трубопровод","teplo": "1","teploPr": "1","reg_t": "Подающий трубопровод","message": "","h": "55"}}</span><span class="spanHiddenNasos">1</span></div></div>
                        <div class="blockSystem"><div class="tpBlockStl"><div class="heatNetworkCls" style="top: calc(66px + 0px);">                                <div class="joiningFeederLine"><div class="heatLine"></div><i>Присоединение к подающему</i> <span>трубопроводу по мосту</span></div>                                <span data-scid="2" class="thermalNetwork">Система отопления</span>                                <div class="attachReverseLine"><div class="coldLine"></div><i>Присоединение к обратному</i> <span>трубопроводу по мосту</span></div>                            </div></div></div>
                    </div><div class="strongBlock strongBlockTop"><div class="MapBlockDrop blockPodpitkyList ui-droppable" onclick=""><div class="dropBlock draggable block_podpitky"><h3 data-scid="2"><div class="divBotPosition"><i class="fa fa-pencil"></i> <i class="fa fa-trash" onclick="deleteBlock(this)"></i></div>Блок подпитки</h3><img class="blockImg" src="/usr/modules/selection/blockConstruct/images/block_podpitky/2.svg" data-podpitka-pov="2" data-t="0" data-podpitka="55" data-podpitka-h="0" data-left="0" data-lile="0" data-type="block_podpitky" data-id="23" style="width: 525px" data-h1="55" data-h2="88" data-c1="91" data-c2="91" alt=""><span class="spanHidden" data-type="block_podpitky">{"block_podpitky": {"povNasos": "MHI","id": "23","klapanSol": "Danfoss","rasshirBak": "Wester","message": "","nasosStan": "2"}}</span></div></div></div><div class="strongBlock"><div class="MapBlockDrop ui-droppable"></div><div class="blockSystem"></div></div><div class="strongBlock"><div class="MapBlockDrop ui-droppable"></div><div class="blockSystem"></div></div>
                </div>
            </div><div class="clear"></div>

            <div class="clear"></div>

            <svg id="svg-line">
            <line></line><line x1="24" y1="81" x2="122" y2="80.75" stroke-width="2" class="line line-red"></line><line x1="24" y1="143" x2="122" y2="142.75" stroke-width="2" class="line line-blue"></line><line x1="1185" y1="80.75" x2="1265" y2="80.75" stroke-width="2" class="line line-red"></line><line x1="1185" y1="142.75" x2="1265" y2="142.75" stroke-width="2" class="line line-blue"></line><line x1="615" y1="80.75" x2="635" y2="80.75" stroke-width="2" class="line line-red"></line><line x1="550" y1="142.75" x2="635" y2="142.75" stroke-width="2" class="line line-blue"></line><line x1="517" y1="313.75" x2="635" y2="313.75" stroke-width="2" class="line line-blue"></line><line x1="505" y1="228.75" x2="517" y2="228.75" stroke-width="2" class="line line-blue"></line><line x1="517" y1="313.75" x2="635" y2="313.75" stroke-width="2" class="line line-blue"></line><line x1="517" y1="228.75" x2="517" y2="313.75" stroke-width="2" class="line line-blue"></line><line x1="1027" y1="161.75" x2="1027" y2="313.75" stroke-width="2" class="line line-blue"></line><line x1="505" y1="80.75" x2="615" y2="80.75" stroke-width="2" class="line line-red"></line><line x1="615" y1="80.75" x2="615" y2="80.75" stroke-width="2" class="line line-red"></line><line x1="505" y1="142.75" x2="550" y2="142.75" stroke-width="2" class="line line-blue"></line><line x1="550" y1="142.75" x2="550" y2="142.75" stroke-width="2" class="line line-blue"></line>
        </svg>
            <div id="svg-point"></div>
        </div>

    </div>
    <div id="blockOneRightWrap" style="display: none;">
        <div class="MapBlockOneImg" style="">
            <div class="mapOneBlockDrop blockActiveHover ui-droppable">
                <div class="divBotPosition"><i class="fa fa-pencil"></i> <i class="fa fa-trash" onclick="deleteBlockOne(this)"></i></div>
                <div class="insertBlockNew insertBlockNull" onclick="menuType()">
                    

                    <div class="clickDp" style="display: none;">
                        <div class="iconHover"></div><span>Вставить блок</span></div>
                </div>

            </div>
        </div>
    </div>
</div>';
	exit;
}