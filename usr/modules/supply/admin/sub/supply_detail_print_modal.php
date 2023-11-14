<?php

function print_modal_supply_detail(){
    global $adminTpl;
    $adminTpl->footIncludes[] = '<script src="/usr/modules/supply/admin/asset/js/supply_detail_print_modal.js"></script>';
}