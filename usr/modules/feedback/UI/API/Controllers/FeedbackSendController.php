<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Feedback\UI\API\Controllers;

use Feedback\UI\API\Requests\SendRequest;
use Boot\System\Core\Abstracts\Controllers\ApiController;

class FeedbackSendController extends ApiController
{
    public function send(SendRequest $request)
    {
        $items = app(SendAction::class)->run($request);
        return $this->transform($items, SendTransformer::class);
    }
}
