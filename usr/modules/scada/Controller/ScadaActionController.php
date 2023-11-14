<?php

namespace Scada\Controller;

use \Boot\Controller\AbstractController;
use \Boot\Services\Request;

class ScadaActionController extends AbstractController{

    static function store(Request $request){
        $scada_action = \Scada\Models\ScadaActions::create($request->request->all());
        echo $scada_action->toJson();
    }
}