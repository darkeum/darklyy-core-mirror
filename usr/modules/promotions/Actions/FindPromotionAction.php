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
use Promotions\Tasks\FindPromotionTask;
use Promotions\UI\API\Requests\FindPromotionRequest;

class FindPromotionAction extends Action
{

  /**
   * Обработчик действия
   *
   * @param  mixed $request
   * @return array
   */
  public function run(FindPromotionRequest $request)
  {
    return app(FindPromotionTask::class)->run($request->id);
  }
}
