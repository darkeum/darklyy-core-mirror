<?php

namespace Control\Controller;

use \Control\Grid\ControlPayGrid;
use \Control\Grid\ControlPayAllGrid;
use \Boot\System\Http\Request;

class ControlPayController extends ControlController{
    static function index(){
        $grid = new ControlPayGrid;
        display('@control/manager/index.html.twig', [
            'grid' =>  $grid->render(),
        ]);
    }

    static function get_buttons_path(){
        return "@control/manager/show/control_pay_buttons";
    }
}