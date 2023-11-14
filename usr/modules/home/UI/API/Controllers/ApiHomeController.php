<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description Login Controller
*/

namespace Home\UI\API\Controllers;

use Home\Actions\ApiHomeAction;
use Boot\Controller\AbstractController;

class ApiHomeController extends AbstractController
{    
    /**
     * Вывод данных для личного кабинета
     *
     * @return array
     */
    public function dashboard()
    {
        return app(ApiHomeAction::class)->run();       
    }
}
