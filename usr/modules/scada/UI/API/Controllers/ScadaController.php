<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\UI\API\Controllers;

use Scada\Actions\ApiScadaTags;
use Scada\Actions\ApiScadaView;
use Scada\Actions\ApiScadaAction;
use Scada\Actions\ScadaAllAction;
use Scada\UI\API\Requests\ScadaAllRequest;
use Scada\UI\API\Requests\ScadaInfoRequest;
use Boot\Abstracts\Controllers\ApiController;
use Scada\UI\API\Requests\ScadaActionRequest;
use Scada\UI\API\Transformers\ScadaTransformer;

class ScadaController extends ApiController
{

  public function index(ScadaAllRequest $request)
  {
    $items = app(ScadaAllAction::class)->run();
    return $this->transform($items, ScadaTransformer::class);
  }

  public function action(ScadaActionRequest $request)
  {
    return app(ApiScadaAction::class)->run($request);
  }

  public function tags(ScadaInfoRequest $request)
  {
    return app(ApiScadaTags::class)->run($request);
  }

  public function view(ScadaInfoRequest $request)
  {
    return app(ApiScadaView::class)->run($request);
  }
}
