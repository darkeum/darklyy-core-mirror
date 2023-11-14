<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Certificates Public Controller
*/

namespace Scada\Controller;

use \Boot\Controller\AbstractController;
use \Boot\Services\Request;

class PublicActionController extends AbstractController
{

  static function store(Request $request)
  {
    $scada_action = \Scada\Models\ScadaActions::create($request->request->all());
    echo $scada_action->toJson();
  }
}