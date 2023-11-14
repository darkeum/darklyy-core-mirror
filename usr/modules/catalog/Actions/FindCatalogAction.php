<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Catalog\Actions;

use Boot\Abstracts\Actions\Action;
use Catalog\Tasks\FindCatalogTask;
use Catalog\UI\API\Requests\FindCatalogRequest;

class FindCatalogAction extends Action
{

  /**
   * Обработчик действия
   *
   * @param  mixed $request
   * @return array
   */
  public function run(FindCatalogRequest $request)
  {
    return app(FindCatalogTask::class)->run($request->id);
  }
}
