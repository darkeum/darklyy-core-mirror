<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\UI\API\Controllers;

use Boot\Abstracts\Controllers\ApiController;

class ScadaClientController extends ApiController
{

  public function settings()
  {
    return [
      "language" => 'ru',
      "uiPort" => 1881,
      "secureEnabled" => false,
      "tokenExpiresIn" => "1h",
      "smtp" => [],

    ];
  }
}
