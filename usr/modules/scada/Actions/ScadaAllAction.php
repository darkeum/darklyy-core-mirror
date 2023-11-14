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

use Boot\Abstracts\Actions\Action;
use Scada\Tasks\ScadaAllTask;

class ScadaAllAction extends Action
{
  /**
   * Обработчик действия
   *
   * @param  mixed $request
   * @return array
   */
  public function run()
  {
    return app(ScadaAllTask::class)->run();		
  }
}