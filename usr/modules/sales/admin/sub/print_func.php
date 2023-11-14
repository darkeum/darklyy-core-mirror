<?php

function print_init(){
    print_modal();
}

function print_modal(){
    global $adminTpl;
    $adminTpl->footIncludes[] = '<script src="/usr/modules/sales/admin/assets/js/print_modal.js?'.time().'"></script>';
}