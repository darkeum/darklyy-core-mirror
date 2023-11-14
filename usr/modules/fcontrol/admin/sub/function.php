<?php

//require_once(ROOT."usr/modules/fcontrol/admin/sub/function.php");

function pay_reason_list($id = "all"){
    $data = [
        'Оплачено по акту сверки',
        'Дублируется',
        'Не актуально',
        'Другое',
    ];

    if(empty($id) && $id != 0){
        return $id;
    }

    return $id == "all" ? $data : $data[$id];
}

function pay_status_view($pay_status, $pay_reason = ""){
    $status = "";
    switch($pay_status){
        default:
            $status = '<font color="blue">Статут - '.$pay_status.' - неизвестный</font>';
        break;
        case 0:
            $status = '<font color="red">Не оплачен</font>';
        break;
        case 1:
            $status = '<font color="green">Оплачен</font>';
        break;
        case 2:
            $status = '<font color="#8925f7">Отмена оплаты по причине '.pay_reason_list($pay_reason).'</font>';
        break;
        case 3:
            $status = '<font class="text-warning-dark">Частичная оплата</font>';
        break;
    }
    return $status;
}