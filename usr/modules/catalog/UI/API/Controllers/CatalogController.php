<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description Valve Calculation Api Controller
*/

namespace Catalog\UI\API\Controllers;

use Catalog\Actions\FindCatalogAction;
use Catalog\Actions\GetCatalogAction;
use Catalog\UI\API\Requests\FindCatalogRequest;
use Catalog\UI\API\Requests\GetAllCatalogRequest;
use Catalog\UI\API\Transformers\CatalogTransformer;
use Boot\System\Core\Abstracts\Controllers\ApiController;

class CatalogController extends ApiController
{
    public function list(GetAllCatalogRequest $request)
    {
        $items = app(GetCatalogAction::class)->run($request);
        return $this->transform($items, CatalogTransformer::class);
    }

    public function show(FindCatalogRequest $request)
    {
        $admin = app(FindCatalogAction::class)->run($request);
        return $this->transform($admin, CatalogTransformer::class);
    }   
}
