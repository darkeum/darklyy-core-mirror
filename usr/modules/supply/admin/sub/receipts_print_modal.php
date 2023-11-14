<?php

function print_modal(){
    global $adminTpl;
    $adminTpl->footIncludes[] = '<script src="/usr/modules/supply/admin/asset/js/receipts_print_modal.js"></script>';
}