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

namespace Promotions\UI\API\Controllers;

use Promotions\Actions\CreateOrderAction;
use Promotions\Actions\FindPromotionAction;
use Promotions\Actions\GetPromotionsAction;
use Promotions\UI\API\Requests\FindPromotionRequest;
use Promotions\UI\API\Requests\GetAllPromotionsRequest;
use Promotions\UI\API\Transformers\PromotionTransformer;
use Boot\System\Core\Abstracts\Controllers\ApiController;
use Promotions\UI\API\Requests\CreateOrderPromotionRequest;

class PromotionsController extends ApiController
{
    public function index(GetAllPromotionsRequest $request)
    {
        $items = app(GetPromotionsAction::class)->run();
        return $this->transform($items, PromotionTransformer::class);
    }

    public function show(FindPromotionRequest $request)
    {
        $admin = app(FindPromotionAction::class)->run($request);
        return $this->transform($admin, PromotionTransformer::class);
    }

    public function order(CreateOrderPromotionRequest $request)
    {
        return app(CreateOrderAction::class)->run($request);
    }

    
}
