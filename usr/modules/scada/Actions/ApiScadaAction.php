<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\Actions;

use Boot\Support\Facades\Auth;
use Scada\Tasks\ScadaActionTask;
use Boot\Abstracts\Actions\Action;
use Scada\UI\API\Requests\ScadaActionRequest;

class ApiScadaAction extends Action
{
  /**
   * Обработчик действия
   *
   * @param  mixed $request
   * @return array
   */
  public function run(ScadaActionRequest $request): array
  {
    $sanitizedData = $request->sanitizeInput([
      'tag_id',
      'value',
      'uid' => Auth::id(),
    ]);

    return (new ScadaActionTask)->run($sanitizedData);
  }
}
