<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Notifications\UI\API\Controllers;

use Boot\Controller\AbstractController;
use Notifications\Actions\ApiGetNotifications;

class ApiNotificationsController extends AbstractController
{    
    /**
     * Вывод данных для личного кабинета
     *
     * @return array
     */
    public function index()
    {
        return app(ApiGetNotifications::class)->run();       
    }
}
