<?php
function fastBar($attr = []){
    global $adminTpl;
    echo \FastBar::style();
    echo \FastBar::template($attr);
    $adminTpl->footIncludes[] = \FastBar::script();
}
