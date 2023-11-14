<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\Actions;

use Scada\Tasks\ScadaViewTask;
use Boot\Abstracts\Actions\Action;
use Scada\UI\API\Requests\ScadaInfoRequest;

class ApiScadaView extends Action
{
  /**
   * Обработчик действия
   *
   * @param  mixed $request
   * @return array
   */
  public function run(ScadaInfoRequest $request): array
  {
    $sanitizedData = $request->sanitizeInput([
      'id',
    ]);

    return (new ScadaViewTask)->run($sanitizedData);
  }
}
