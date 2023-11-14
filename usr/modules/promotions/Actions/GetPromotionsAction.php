<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Promotions\Actions;

use Boot\Abstracts\Actions\Action;
use Promotions\Tasks\GetPromotionsTask;

class GetPromotionsAction extends Action
{
  /**
   * Обработчик действия
   *
   * @param  mixed $request
   * @return array
   */
  public function run()
  {
    return app(GetPromotionsTask::class)->run();		
  }
}