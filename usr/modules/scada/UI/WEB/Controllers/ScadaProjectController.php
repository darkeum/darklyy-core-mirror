<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\UI\WEB\Controllers;

use \Boot\Abstracts\Controllers\ApiController;
use \Scada\Actions\ApiScadaAction;
use \Scada\UI\API\Requests\ScadaActionRequest;

class ScadaProjectController extends ApiController
{
  public function calculate(ScadaActionRequest $request)
  {
    $result = (new ApiScadaAction)->run($request);
    return $result;
  }
}