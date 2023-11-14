<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/ 
 
if (!defined('ACCESS')) {
    header('Location: /');
    exit;
}

global $lang, $core;
loadConfig('feedback'); 
if(!empty($feedback_conf['keywords']))
{
	$core->tpl->keywords =$feedback_conf['keywords'];
}
if(!empty($feedback_conf['description']))
{
	$core->tpl->description = $feedback_conf['description'];
}
switch(isset($url[1]) ? $url[1] : null) 
{
	default:
		set_title(array($lang['feedback']));
		$core->tpl->loadFile('feedback');
		$core->tpl->setVar('CAPTCHA', captcha_image());
		$core->tpl->setVar('FILE_SIZE', formatfilesize($feedback_conf['file_size']));
		$core->tpl->setVar('FORMATS', $feedback_conf['formats']);
		$core->tpl->sources = if_sets("#\\[load_attach\\](.*?)\\[/load_attach\\]#is", $core->tpl->sources, $feedback_conf['allow_attach']);
		$core->tpl->end();
		break;	
	
    case "power":
        $core->tpl->js_foot[] = '<script src="/usr/modules/converter/asset/power.js"></script>';
		$page_title = 'Перевод единиц мощности';
		$bread = '<li><a href="/converter" >Инструменты для проектировщика</a><span class="go-page"></span></li>';
		$core->tpl->loadFile('converter/power');
		$items = $core->tpl->return_end();
		$core->tpl->loadFile('converter/carcase');
		include('carcase_tag.php');	
		$core->tpl->end();
		break;
        
		
           
		
		
    case "table_shevelow":	
		if (isset($url[2]) && $url[2] == 'send')
		{
function velocity($Q, $dp)
{
    return round(($Q / ((3.14 * pow($dp, 2)) / 4) / 1000), 3);
}

function steel($type, $v, $dp, $vi)
{
    if ($type == 0) { //0 - неновая
        if ($v / $vi >= 9.5 * pow(10, -5)) {
            $lambda = 0.021 / pow($dp, 0.3);
        } else {
            $lambda = 1 / pow($dp, 0.3) * pow((1.5 * pow(10, -6) + $vi / $v), 0.3);
        }
    } else {
        $lambda = 0.312 / pow($dp, 0.226) * pow(1.9 * pow(10, -6) + $vi / $v, 0.226);
    }
    return $lambda * 1 / $dp * pow($v, 2) / 19.62;
}

function cast_iron($type, $v, $dp)
{
    if ($type == 0) { //0 - неновая
        if ($v >= 1.2) {
            return 0.00107 * (pow($v, 2) / pow($dp, 1.3));
        } else {
            return 0.000912 * (pow($v, 2) / pow($dp, 1.3)) * pow((1 + 0.867 / $v), 0.3);
        }
    } else {
        $lambda = (0.0144 / pow($dp, 0.284)) * pow((1 + 2.36 / $v), 0.284);
        return $lambda * 1 / $dp * pow($v, 2) / 19.62;
    }
}

function asbestos_cement($v, $dp)
{
    return 0.000561 * pow($v, 2) / pow($dp, 1.190) * pow((1 + 3.51 / $v), 0.190);
}

function plastic($v, $dp)
{
    return 0.000685 * pow($v, 1.774) / pow($dp, 1.226);
}

function reinforced_concrete($v, $dp, $fi)
{
    return 0.000802 * $fi * pow($v, 2) / pow($dp, 1.190) * pow((1 + 3.51 / $v), 0.190);
}

function fiberglass($v, $dp)
{
    return 0.0146 * pow(($v * $dp), -0.226) * pow($v, 2) / (2 * 9.8 * $dp);
}

function glass($v, $dp)
{
    return 0.000745 * pow($v, 1.774) / pow($dp, 1.226);
}

function copper1($v, $dp, $vi)
{
    return 0.0161 * pow($vi, 0.25) * pow($v, 1.75) * pow($dp, -1.25);
}

function copper2($v, $dp, $vi)
{
    return 0.051 * (((6.52 * log10($dp) + log10($v / $vi)) / (115 * pow(log10($dp), 2))) + ((0.66 * log10($dp) + 0.1 * log10($v / $vi)) / (log10($v / $vi) * log10($dp)))) * (pow($v, 2) / $dp);
}

function HTrub($L, $i, $v, $sum)
{
    return $L * $i + (pow($v, 2) / 19.62) * $sum;
}
function sqrtLambda($v, $dp, $vi){
    $K = pow(10, -5);
    $ReF = $dp * $v / $vi;
    $ReKV = 500 * $dp / $K;
    $b = 1 + (log10($ReF) / log10($ReKV));
    return (0.5 * (($b / 2 + 1.312 * (2 - $b) * log10(3.7 * $dp / $K)) / (log10($ReF) - 1))) / (log10(3.7 * $dp / $K));
}
function Rend($v, $dp, $vi){
    $lambda = sqrtLambda($v, $dp, $vi);
    return ((pow($lambda, 2) * pow($v, 2)) / (2 * $dp)) * pow(10, 3);
}
function polyethylene($v, $dp, $vi){
    $lambda = sqrtLambda($v, $dp, $vi);
    return ((pow($lambda, 2) * pow($v, 2)) / (19.62 * $dp));
}
switch ($_POST["nameType"]) {
    case 1:
        if (!isset($_POST["etc1"])) {
            if ($_POST["type1"] == 0) {
                $dp1 = $_POST["dh1"] - 2 * $_POST["depth1"] - 1;
            } else {
                $dp1 = $_POST["dh1"] - 2 * $_POST["depth1"];
            }
        } else {
            $dp1 = $_POST["dp1"];
        }
        $dpm1 = $dp1 / 1000;
        $mas1 = [
            40 => 0.60,
            45 => 0.60,
            50 => 0.55,
            55 => 0.51,
            60 => 0.47,
            65 => 0.44,
            70 => 0.41,
            75 => 0.38,
            80 => 0.36,
            85 => 0.34,
            90 => 0.32,
            95 => 0.30
        ];
        if ($_POST["temp1"] == 0) {
            $vi1 = 1.3 * pow(10, -6);
        } else {
            $vi1 = $mas1[$_POST["temp1s"]] * pow(10, -6);
        }
        if ($_POST["units1"] == 0) {
            $Q1 = $_POST["Q1"];
        } else {
            $Q1 = $_POST["Q1"] / 3.6;
        }
        $V1 = velocity($Q1, $dpm1);
        $ires1 = steel($_POST["type1"], $V1, $dpm1, $vi1);
        $nameType = $_POST["nameType"];

        echo '{"dp":' . $dp1 . ',"V":' . $V1 . ',"ires":' . round($ires1 * 1000, 3) . ',"nameType":' . $nameType . '}';
        break;
    case 2:
        if (!isset($_POST["etc2"])) {
            if ($_POST["type2"] == 0) {
                $dp2 = $_POST["dh2"] - 2 * $_POST["depth2"] - 1;
            } else {
                $dp2 = $_POST["dh2"] - 2 * $_POST["depth2"];
            }
        } else {
            $dp2 = $_POST["dp2"];
        }
        $dpm2 = $dp2 / 1000;
        $mas2 = [
            40 => 0.60,
            45 => 0.60,
            50 => 0.55,
            55 => 0.51,
            60 => 0.47,
            65 => 0.44,
            70 => 0.41,
            75 => 0.38,
            80 => 0.36,
            85 => 0.34,
            90 => 0.32,
            95 => 0.30
        ];
        if ($_POST["temp2"] == 0) {
            $vi2 = 1.3 * pow(10, -6);
        } else {
            $vi2 = $mas2[$_POST["temp2s"]] * pow(10, -6);
        }
        if ($_POST["units2"] == 0) {
            $Q2 = $_POST["Q2"];
        } else {
            $Q2 = $_POST["Q2"] / 3.6;
        }
        $V2 = velocity($Q2, $dpm2);
        $ires2 = steel($_POST["type2"], $V2, $dpm2, $vi2);
        $nameType = $_POST["nameType"];

        echo '{"dp":' . $dp2 . ',"V":' . $V2 . ',"ires":' . round($ires2 * 1000, 3) . ',"nameType":' . $nameType . '}';
        break;
    case 3:
        if (!isset($_POST["etc3"])) {
            $mas3 = [
                50 => 51.6,
                80 => 82.6,
                100 => 102,
                125 => 127.2,
                150 => 152.4,
                200 => 202.6,
                250 => 253,
                300 => 304.4,
                350 => 352.4,
                400 => 401.4,
                450 => 450.6,
                500 => 500.8,
                600 => 600.2,
                700 => 699.4,
                800 => 799.8,
                900 => 899.2,
                1000 => 998.4,
                1200 => 1199.2
            ];
            $dp3 = $mas3[$_POST["CP3"]];
            if ($_POST["type3"] == 1) {
                if ($dp3 < 300) {
                    $dp3 += 1;
                }
            }
        } else {
            $dp3 = $_POST["dp3"];
        }
        $dpm3 = $dp3 / 1000;
        if ($_POST["units3"] == 0) {
            $Q3 = $_POST["Q3"];
        } else {
            $Q3 = $_POST["Q3"] / 3.6;
        }
        $V3 = velocity($Q3, $dpm3);
        $ires3 = cast_iron($_POST["type3"], $V3, $dpm3);
        $nameType = $_POST["nameType"];

        echo '{"dp":' . $dp3 . ',"V":' . $V3 . ',"ires":' . round($ires3 * 1000, 3) . ',"nameType":' . $nameType . '}';
        break;
    case 4:
        if (!isset($_POST["etc4"])) {
            $vt6type1 = [
                100 => 104,
                150 => 146,
                200 => 196,
                250 => 244,
                300 => 289,
                350 => 334,
                400 => 381,
                500 => 473
            ];
            $vt6type2 = [
                200 => 200,
                250 => 248,
                300 => 292,
                350 => 337,
                400 => 385,
                500 => 476
            ];
            $vt6type3 = [
                200 => 196,
                300 => 289
            ];
            $vt9type1 = [
                100 => 100,
                150 => 141,
                200 => 189,
                250 => 235,
                300 => 279,
                350 => 322,
                400 => 368,
                500 => 456
            ];
            $vt9type2 = [
                200 => 196,
                250 => 242,
                300 => 286,
                350 => 329,
                400 => 377,
                500 => 466
            ];
            $vt9type3 = [
                200 => 189,
                300 => 279
            ];
            $vt12type1 = [
                100 => 96,
                150 => 135,
                200 => 181,
                250 => 228,
                300 => 270,
                350 => 312,
                400 => 356,
                500 => 441
            ];
            $vt12type2 = [
                200 => 188,
                250 => 234,
                300 => 276,
                350 => 317,
                400 => 363,
                500 => 450
            ];
            $vt12type3 = [
                200 => 181,
                300 => 270
            ];
            $vt15type2 = [
                200 => 180,
                250 => 226,
                300 => 267,
                350 => 307,
                400 => 352,
                500 => 436
            ];
            $vt15type3 = [
                200 => 176,
                300 => 256
            ];
            if ($_POST["VT4"] == 0) {
                if ($_POST["type4"] == 1) {
                    $dp4 = $vt6type1[$_POST["CP4"]];
                }
                if ($_POST["type4"] == 2) {
                    $dp4 = $vt6type2[$_POST["CP4"]];
                }
                if ($_POST["type4"] == 3) {
                    $dp4 = $vt6type3[$_POST["CP4"]];
                }
            }
            if ($_POST["VT4"] == 1) {
                if ($_POST["type4"] == 1) {
                    $dp4 = $vt9type1[$_POST["CP4"]];
                }
                if ($_POST["type4"] == 2) {
                    $dp4 = $vt9type2[$_POST["CP4"]];
                }
                if ($_POST["type4"] == 3) {
                    $dp4 = $vt9type3[$_POST["CP4"]];
                }
            }
            if ($_POST["VT4"] == 2) {
                if ($_POST["type4"] == 1) {
                    $dp4 = $vt12type1[$_POST["CP4"]];
                }
                if ($_POST["type4"] == 2) {
                    $dp4 = $vt12type2[$_POST["CP4"]];
                }
                if ($_POST["type4"] == 3) {
                    $dp4 = $vt12type3[$_POST["CP4"]];
                }
            }
            if ($_POST["VT4"] == 3) {
                if ($_POST["type4"] == 2) {
                    $dp4 = $vt15type2[$_POST["CP4"]];
                }
                if ($_POST["type4"] == 3) {
                    $dp4 = $vt15type3[$_POST["CP4"]];
                }
            }
        } else {
            $dp4 = $_POST["dp4"];
        }
        $dpm4 = $dp4 / 1000;
        if ($_POST["units4"] == 0) {
            $Q4 = $_POST["Q4"];
        } else {
            $Q4 = $_POST["Q4"] / 3.6;
        }
        $V4 = velocity($Q4, $dpm4);
        $ires4 = asbestos_cement($V4, $dpm4);
        $nameType = $_POST["nameType"];

        echo '{"dp":' . $dp4 . ',"V":' . $V4 . ',"ires":' . round($ires4 * 1000, 3) . ',"nameType":' . $nameType . '}';
        break;
    case 5:
        if (!isset($_POST["etc5"])) {
            $SDR41 = [
                63 => 59,
                75 => 71,
                90 => 85.6,
                110 => 104.6,
                125 => 118.8,
                140 => 133,
                160 => 152,
                180 => 171.2,
                200 => 190.2,
                250 => 237.6,
                280 => 266.2,
                315 => 299.6,
                355 => 337.6,
                400 => 380.4,
                450 => 428,
                500 => 475.4,
                560 => 532.6,
                630 => 599.2,
                710 => 675.2,
                800 => 760.8,
                900 => 856,
                1000 => 951,
                1200 => 1141.2
            ];
            $SDR26 = [
                40 => 36,
                50 => 46,
                63 => 58,
                75 => 69.2,
                90 => 83,
                110 => 101.6,
                125 => 115.4,
                140 => 129.2,
                160 => 147.6,
                180 => 166.2,
                200 => 184.6,
                250 => 230.8,
                280 => 258.6,
                315 => 290.8,
                355 => 327.8,
                400 => 369.4,
                450 => 415.6,
                500 => 461.8,
                560 => 517.2,
                630 => 581.8,
                710 => 655.6,
                800 => 738.8,
                900 => 831.2,
                1000 => 923.6,
                1200 => 1108.2
            ];
            $SDR21 = [
                32 => 28,
                40 => 36,
                50 => 45.2,
                63 => 57,
                75 => 67.8,
                90 => 81.4,
                110 => 99.4,
                125 => 113,
                140 => 126.6,
                160 => 144.6,
                180 => 162.8,
                200 => 180.8,
                250 => 226.2,
                280 => 253.2,
                315 => 285,
                355 => 321.2,
                400 => 361.8,
                450 => 407,
                500 => 452.2,
                560 => 506.6,
                630 => 570,
                710 => 642.2,
                800 => 723.8,
                900 => 814.2,
                1000 => 904.6,
                1200 => 1085.6
            ];
            $SDR176 = [
                25 => 21,
                32 => 28,
                40 => 35.4,
                50 => 44.2,
                63 => 55.8,
                75 => 66.4,
                90 => 79.8,
                110 => 97.4,
                125 => 110.8,
                140 => 124,
                160 => 141.8,
                180 => 159.6,
                200 => 177.2,
                250 => 221.6,
                280 => 248.2,
                315 => 279.2,
                355 => 314.8,
                400 => 354.6,
                450 => 399,
                500 => 443.4,
                560 => 496.6,
                630 => 558.6,
                710 => 629.6,
                800 => 709.4,
                900 => 798,
                1000 => 886.8
            ];
            $SDR17 = [
                32 => 28,
                40 => 35.2,
                50 => 44,
                63 => 55.4,
                75 => 66,
                90 => 79.2,
                110 => 96.8,
                125 => 110.2,
                140 => 123.4,
                160 => 141,
                180 => 158.6,
                200 => 176.2,
                250 => 220.4,
                280 => 246.8,
                315 => 277.6,
                355 => 312.8,
                400 => 352.6,
                450 => 396.6,
                500 => 440.6,
                560 => 493.6,
                630 => 555.2,
                710 => 625.8,
                800 => 705.2,
                900 => 793.4,
                1000 => 881.4
            ];
            $SDR136 = [
                25 => 21,
                32 => 27.2,
                40 => 34,
                50 => 42.6,
                63 => 53.6,
                75 => 63.8,
                90 => 76.6,
                110 => 93.8,
                125 => 106.6,
                140 => 119.4,
                160 => 136.4,
                180 => 153.4,
                200 => 170.6,
                250 => 213.2,
                280 => 238.8,
                315 => 268.6,
                355 => 302.8,
                400 => 341.2,
                450 => 383.8,
                500 => 426.4,
                560 => 477.6,
                630 => 537.4,
                710 => 605.6,
                800 => 682.4
            ];
            $SDR11 = [
                16 => 12,
                20 => 16,
                25 => 20.4,
                32 => 26,
                40 => 32.6,
                50 => 40.8,
                63 => 51.4,
                75 => 61.4,
                90 => 73.6,
                110 => 90,
                125 => 102.2,
                140 => 114.6,
                160 => 130.8,
                180 => 147.2,
                200 => 163.6,
                250 => 204.6,
                280 => 229.2,
                315 => 257.8,
                355 => 290.6,
                400 => 327.4,
                450 => 368.2,
                500 => 409.2,
                560 => 458.4,
                630 => 515.6
            ];
            $SDR9 = [
                16 => 12,
                20 => 15.4,
                25 => 19.4,
                32 => 24.8,
                40 => 31,
                50 => 38.8,
                63 => 48.8,
                75 => 58.2,
                90 => 69.8,
                110 => 85.4,
                125 => 97,
                140 => 108.6,
                160 => 124.2,
                180 => 139.8,
                200 => 155.2,
                250 => 194.2,
                280 => 217.4,
                315 => 244.6,
                355 => 275.6,
                400 => 327.4,
                450 => 310.6,
                500 => 388.4
            ];
            $SDR6 = [
                10 => 6,
                12 => 8,
                16 => 10.6,
                20 => 13.2,
                25 => 16.6,
                32 => 21.2,
                40 => 26.6,
                50 => 33.4,
                63 => 42,
                75 => 50,
                90 => 60,
                110 => 73.4,
                125 => 83.4
            ];
            switch ($_POST["MRS5"]) {
                case 1:
                    switch ($_POST["SDR5"]) {
                        case 1:
                            $dp5 = $SDR21[$_POST["CP5"]];
                            break;
                        case 2:
                            $dp5 = $SDR136[$_POST["CP5"]];
                            break;
                        case 3:
                            $dp5 = $SDR9[$_POST["CP5"]];
                            break;
                        case 4:
                            $dp5 = $SDR6[$_POST["CP5"]];
                            break;
                    }
                    break;
                case 2:
                    switch ($_POST["SDR5"]) {
                        case 1:
                            $dp5 = $SDR41[$_POST["CP5"]];
                            break;
                        case 2:
                            $dp5 = $SDR26[$_POST["CP5"]];
                            break;
                        case 3:
                            $dp5 = $SDR176[$_POST["CP5"]];
                            break;
                        case 4:
                            $dp5 = $SDR11[$_POST["CP5"]];
                            break;
                    }
                    break;
                case 3:
                    switch ($_POST["SDR5"]) {
                        case 1:
                            $dp5 = $SDR26[$_POST["CP5"]];
                            break;
                        case 2:
                            $dp5 = $SDR21[$_POST["CP5"]];
                            break;
                        case 3:
                            $dp5 = $SDR176[$_POST["CP5"]];
                            break;
                        case 4:
                            $dp5 = $SDR17[$_POST["CP5"]];
                            break;
                        case 5:
                            $dp5 = $SDR136[$_POST["CP5"]];
                            break;
                        case 6:
                            $dp5 = $SDR11[$_POST["CP5"]];
                            break;
                        case 7:
                            $dp5 = $SDR9[$_POST["CP5"]];
                            break;
                    }
                    break;
                case 4:
                    switch ($_POST["SDR5"]) {
                        case 1:
                            $dp5 = $SDR17[$_POST["CP5"]];
                            break;
                        case 2:
                            $dp5 = $SDR136[$_POST["CP5"]];
                            break;
                        case 3:
                            $dp5 = $SDR11[$_POST["CP5"]];
                            break;
                    }
                    break;
            }
        } else {
            $dp5 = $_POST["dp5"];
        }
        $dpm5 = $dp5 / 1000;
        if ($_POST["units5"] == 0) {
            $Q5 = $_POST["Q5"];
        } else {
            $Q5 = $_POST["Q5"] / 3.6;
        }

        $V5 = velocity($Q5, $dpm5);
        $ires5 = plastic($V5, $dpm5);
        $nameType = $_POST["nameType"];

        echo '{"dp":' . $dp5 . ',"V":' . $V5 . ',"ires":' . round($ires5 * 1000, 3) . ',"nameType":' . $nameType . '}';
        break;
    case 6:
        if (!isset($_POST["etc6"])) {
            $dp6 = $_POST["CP6"];
        } else {
            $dp6 = $_POST["dp6"];
        }
        $dpm6 = $dp6 / 1000;
        if ($_POST["units6"] == 0) {
            $Q6 = $_POST["Q6"];
        } else {
            $Q6 = $_POST["Q6"] / 3.6;
        }

        $V6 = velocity($Q6, $dpm6);
        $fi6 = 1;
        $ires6 = reinforced_concrete($V6, $dpm6, $fi6);
        $nameType = $_POST["nameType"];

        echo '{"dp":' . $dp6 . ',"V":' . $V6 . ',"ires":' . round($ires6 * 1000, 3) . ',"nameType":' . $nameType . '}';
        break;
    case 7:
        if (!isset($_POST["etc7"])) {
            $dp7 = $_POST["CP7"];
        } else {
            $dp7 = $_POST["dp7"];
        }
        $dpm7 = $dp7 / 1000;
        if ($_POST["units7"] == 0) {
            $Q7 = $_POST["Q7"];
        } else {
            $Q7 = $_POST["Q7"] / 3.6;
        }

        $V7 = velocity($Q7, $dpm7);
        $ires7 = fiberglass($V7, $dpm7);
        $nameType = $_POST["nameType"];

        echo '{"dp":' . $dp7 . ',"V":' . $V7 . ',"ires":' . round($ires7 * 1000, 3) . ',"nameType":' . $nameType . '}';
        break;
    case 8:
        if (!isset($_POST["etc8"])) {
            $mas8 = [
                45 => 37,
                67 => 57,
                93 => 81,
                122 => 108,
                169 => 150,
                221 => 198
            ];
            $dp8 = $mas8[$_POST["CP8"]];
        } else {
            $dp8 = $_POST["dp8"];
        }
        $dpm8 = $dp8 / 1000;
        if ($_POST["units8"] == 0) {
            $Q8 = $_POST["Q8"];
        } else {
            $Q8 = $_POST["Q8"] / 3.6;
        }

        $V8 = velocity($Q8, $dpm8);
        $ires8 = glass($V8, $dpm8);
        $nameType = $_POST["nameType"];

        echo '{"dp":' . $dp8 . ',"V":' . $V8 . ',"ires":' . round($ires8 * 1000, 3) . ',"nameType":' . $nameType . '}';
        break;
    case 9:
        if (!isset($_POST["etc9"])) {
            $dp9 = $_POST["dh9"] - 2 * $_POST["depth9"];
        } else {
            $dp9 = $_POST["dp9"];
        }
        $dpm9 = $dp9 / 1000;
        $mas9 = [
            10 => 1.3,
            50 => 0.55,
            60 => 0.47,
            70 => 0.41,
            80 => 0.36,
            90 => 0.36
        ];
        $plot9 = [
            50 => 990,
            60 => 980,
            70 => 980,
            80 => 970,
            90 => 970
        ];
        if ($_POST["tempName9"] == 1) {
            $vi9 = $mas9[10] * pow(10, -6);
        } else {
            if ($_POST["tempName9"] == 4) {
                if ($_POST["liquid9"] == 0) {
                    $vi9 = $mas9[$_POST["temp9"]] * pow(10, -6);
                    $p9 = $plot9[$_POST["temp9"]];
                } else {
                    $vi9 = $_POST["vi9"] * pow(10, -6);
                    $p9 = $_POST["density9"];
                }
            } else {
                $vi9 = $mas9[$_POST["temp9"]] * pow(10, -6);
            }
        }
        if ($_POST["units9"] == 0) {
            $Q9 = $_POST["Q9"];
        } else {
            $Q9 = $_POST["Q9"] / 3.6;
        }
        $V9 = velocity($Q9, $dpm9);
        switch ($_POST["tempName9"]){
            case 1:
            case 2:
            $ires9 = copper1($V9, $dpm9, $vi9);
                break;
            case 3:
                $ires9 = copper2($V9, $dpm9, $vi9);
                break;
            case 4:
                $ires9 = Rend($V9, $dpm9, $vi9);
                break;
        }
        $R9 = $ires9;
        $kof = [
            1 => 0.5,
            2 => 3.0,
            3 => 0.5,
            4 => 0.5,
            5 => 1.5,
            6 => 1.0,
            7 => 1.5,
            8 => 1.0,
            9 => 3.0,
            10 => 0.5,
            11 => 2.0,
            12 => 0.7
        ];
        $Sum9 = 0;
        for ($j = 1; $j <= 12; $j++) {
            if (isset($_POST["resistance9" . $j])) {
                $Sum9 += $kof[$j] * $_POST["pieces9" . $j];
            }
        }
        if ($Sum9 == 0) {
            $Sum9++;
        }
        if ($_POST["tempName9"] == 4) {
            $Z9 = 0.5 * $p9 * pow($V9, 2) * $Sum9;
            $deltaP9 = $R9 * $_POST["L9"] + $Z9;
            $H9 = $deltaP9;
        } else {
            $H9 = HTrub($_POST["L9"], $ires9, $V9, $Sum9);
        }
        $nameType = $_POST["nameType"];

        echo '{"dp":' . $dp9 . ',"V":' . $V9 . ',"ires":' . round($ires9, 3) . ',"H":' . round($H9, 3) . ',"nameType":' . $nameType . '}';
        break;
    case 10:
        if (!isset($_POST["etc10"])) {
            $SDR10136 = [
                16 => 13.15,
                20 => 16.75,
                25 => 20.95,
                32 => 26.85,
                40 => 33.7,
                50 => 42.35,
                63 => 53.2,
                75 => 63.35,
                90 => 76.15,
                110 => 93.2
            ];
            $SDR1011 = [
                12 => 9.15,
                16 => 12.75,
                18 => 14.35,
                20 => 15.95,
                22 => 17.65,
                25 => 20.05,
                32 => 25.85,
                40 => 32.2,
                50 => 40.35,
                63 => 50.9,
                75 => 60.85,
                90 => 72.95,
                110 => 89.3
            ];
            $SDR109 = [
                12 => 8.95,
                16 => 12.15,
                18 => 13.75,
                20 => 15.05,
                22 => 16.65,
                25 => 19.05,
                32 => 24.35,
                40 => 30.5,
                50 => 38.25,
                63 => 48.1,
                75 => 57.45,
                90 => 68.95,
                110 => 94.4
            ];
            $SDR1074 = [
                12 => 8.35,
                15 => 10.65,
                16 => 11.25,
                18 => 12.65,
                20 => 14.05,
                22 => 15.55,
                25 => 17.55,
                32 => 22.65,
                40 => 28.4,
                50 => 35.55,
                63 => 45,
                75 => 53.55,
                90 => 64.35,
                110 => 78.5
            ];
            $SDR106 = [
                12 => 7.75,
                15 => 9.65,
                16 => 10.25,
                18 => 11.35,
                20 => 12.75,
                22 => 13.95,
                25 => 16.05,
                32 => 20.55,
                40 => 25.9,
                50 => 32.55,
                63 => 41,
                75 => 48.85,
                90 => 58.75,
                110 => 71.8
            ];
            switch ($_POST["SDR10"]){
                case 1:
                    $dp10 = $SDR10136[$_POST["dh10"]];
                    break;
                case 2:
                    $dp10 = $SDR1011[$_POST["dh10"]];
                    break;
                case 3:
                    $dp10 = $SDR109[$_POST["dh10"]];
                    break;
                case 4:
                    $dp10 = $SDR1074[$_POST["dh10"]];
                    break;
                case 5:
                    $dp10 = $SDR106[$_POST["dh10"]];
                    break;
            }
        } else {
            $dp10 = $_POST["dp10"];
        }
        $dpm10 = $dp10 / 1000;
        $mas10 = [
            10 => 1.3,
            40 => 0.66,
            50 => 0.55,
            60 => 0.47,
            70 => 0.41,
            80 => 0.36,
            90 => 0.36
        ];
        $plot10 = [
            50 => 990,
            60 => 980,
            70 => 980,
            80 => 970,
            90 => 970
        ];
        if ($_POST["tempName10"] == 1) {
            $vi10 = $mas10[10] * pow(10, -6);
        } else {
            if ($_POST["tempName10"] == 3) {
                if ($_POST["liquid10"] == 0) {
                    $vi10 = $mas10[$_POST["temp10"]] * pow(10, -6);
                    $p10 = $plot10[$_POST["temp10"]];
                } else {
                    $vi10 = $_POST["vi10"] * pow(10, -6);
                    $p10 = $_POST["density10"];
                }
            } else {
                $vi10 = $mas10[$_POST["temp10"]] * pow(10, -6);
            }
        }
        if ($_POST["units10"] == 0) {
            $Q10 = $_POST["Q10"];
        } else {
            $Q10 = $_POST["Q10"] / 3.6;
        }
        $V10 = velocity($Q10, $dpm10);
        switch ($_POST["tempName10"]){
            case 1:
            case 2:
                $ires10 = polyethylene($V10, $dpm10, $vi10);
                break;
            case 3:
                $ires10 = Rend($V10, $dpm10, $vi10);
                break;
        }
        $R10 = $ires10;
        $kof = [
            1 => 0.5,
            2 => 3.0,
            3 => 0.5,
            4 => 0.5,
            5 => 1.5,
            6 => 1.0,
            7 => 1.5,
            8 => 1.0,
            9 => 3.0,
            10 => 0.5,
            11 => 2.0,
            12 => 0.7
        ];
        $Sum10 = 0;
        for ($j = 1; $j <= 12; $j++) {
            if (isset($_POST["resistance10" . $j])) {
                $Sum10 += $kof[$j] * $_POST["pieces10" . $j];
            }
        }
        if ($Sum10 == 0) {
            $Sum10++;
        }
        if ($_POST["tempName10"] == 4) {
            $Z10 = 0.5 * $p10 * pow($V10, 2) * $Sum10;
            $deltaP10 = $R10 * $_POST["L10"] + $Z10;
            $H10 = $deltaP10;
        } else {
            $H10 = HTrub($_POST["L10"], $ires10, $V10, $Sum10);
        }
        $nameType = $_POST["nameType"];

        echo '{"dp":' . $dp10 . ',"V":' . $V10 . ',"ires":' . round($ires10, 3) . ',"H":' . round($H10, 3) . ',"nameType":' . $nameType . '}';
        break;
    case 11:
        if (!isset($_POST["etc11"])) {
            $mas111 = [
                16 => 12,
                20 => 16,
                26 => 20,
                32 => 26,
                40 => 33,
                50 => 42
            ];
            $dp11 = $mas111[$_POST["dh11"]];
        } else {
            $dp11 = $_POST["dp11"];
        }
        $dpm11 = $dp11 / 1000;
        $mas11 = [
            10 => 1.3,
            40 => 0.66,
            50 => 0.55,
            60 => 0.47,
            70 => 0.41,
            80 => 0.36,
            90 => 0.36
        ];
        $plot11 = [
            50 => 990,
            60 => 980,
            70 => 980,
            80 => 970,
            90 => 970
        ];
        if ($_POST["tempName11"] == 1) {
            $vi11 = $mas11[10] * pow(10, -6);
        } else {
            if ($_POST["tempName11"] == 3) {
                if ($_POST["liquid11"] == 0) {
                    $vi11 = $mas11[$_POST["temp11"]] * pow(10, -6);
                    $p11 = $plot11[$_POST["temp11"]];
                } else {
                    $vi11 = $_POST["vi11"] * pow(10, -6);
                    $p11 = $_POST["density11"];
                }
            } else {
                $vi11 = $mas11[$_POST["temp11"]] * pow(10, -6);
            }
        }
        if ($_POST["units11"] == 0) {
            $Q11 = $_POST["Q11"];
        } else {
            $Q11 = $_POST["Q11"] / 3.6;
        }
        $V11 = velocity($Q11, $dpm11);
        switch ($_POST["tempName11"]){
            case 1:
            case 2:
                $ires11 = polyethylene($V11, $dpm11, $vi11);
                break;
            case 3:
                $ires11 = Rend($V11, $dpm11, $vi11);
                break;
        }
        $R11 = $ires11;
        $kof = [
            1 => 0.5,
            2 => 3.0,
            3 => 0.5,
            4 => 0.5,
            5 => 1.5,
            6 => 1.0,
            7 => 1.5,
            8 => 1.0,
            9 => 3.0,
            10 => 0.5,
            11 => 2.0,
            12 => 0.7
        ];
        $Sum11 = 0;
        for ($j = 1; $j <= 12; $j++) {
            if (isset($_POST["resistance11" . $j])) {
                $Sum11 += $kof[$j] * $_POST["pieces11" . $j];
            }
        }
        if ($Sum11 == 0) {
            $Sum11++;
        }
        if ($_POST["tempName11"] == 4) {
            $Z11 = 0.5 * $p11 * pow($V11, 2) * $Sum11;
            $deltaP11 = $R11 * $_POST["L11"] + $Z11;
            $H11 = $deltaP11;
        } else {
            $H11 = HTrub($_POST["L11"], $ires11, $V11, $Sum11);
        }
        $nameType = $_POST["nameType"];

        echo '{"dp":' . $dp11 . ',"V":' . $V11 . ',"ires":' . round($ires11, 3) . ',"H":' . round($H11, 3) . ',"nameType":' . $nameType . '}';
        break;
}
    exit;
		}
		else
		{
			$page_title = 'Таблица Шевелёва';
			$bread = '<li><a href="/converter" >Инструменты для проектировщика</a><span class="go-page"></span></li>';
            $core->tpl->js_foot[] = '<script src="/usr/modules/converter/asset/table_shevelow.js"></script>';
			$core->tpl->loadFile('converter/table_shevelow');
			$items = $core->tpl->return_end();
			$core->tpl->loadFile('converter/carcase');
			include('carcase_tag.php');			
			$core->tpl->end();
		}
	break;
	
	
	
	
		
}