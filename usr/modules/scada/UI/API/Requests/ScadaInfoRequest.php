<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\UI\API\Requests;

use Boot\Abstracts\Requests\Request;

class ScadaInfoRequest extends Request
{
  protected array $decode = [
    'id',
  ];

  protected array $urlParameters = [
    'id',
  ];

  public function rules(): array
  {
    return [
      'id'         => 'required|exists:\Scada\Models\Scada,id',
    ];
  }
}
