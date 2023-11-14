<?php

namespace Control\Controller;

use \Control\Grid\ControlPayAllGrid;
use \Boot\System\Http\Request;

class ControlPayAllController extends ControlPayController{
    static function index(){
        $grid = new ControlPayAllGrid;
        display('@control/manager/index.html.twig', [
            'grid' =>  $grid->render(),
        ]);
    }
}