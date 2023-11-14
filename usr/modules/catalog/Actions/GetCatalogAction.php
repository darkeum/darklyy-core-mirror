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

use Catalog\Tasks\GetCatalogTask;
use Boot\Abstracts\Actions\Action;
use Catalog\UI\API\Requests\GetAllCatalogRequest;

class GetCatalogAction extends Action
{
  /**
   * Обработчик действия
   *
   * @param  mixed $request
   * @return array
   */
  public function run(GetAllCatalogRequest $request)
  {
    return app(GetCatalogTask::class)->run($request);		
  }
}