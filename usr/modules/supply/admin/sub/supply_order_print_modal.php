<?php

function print_modal_supply_order(){
    global $adminTpl;
    $adminTpl->footIncludes[] = '<script src="/usr/modules/supply/admin/asset/js/supply_order_print_modal.js"></script>';
}