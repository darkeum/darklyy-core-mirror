<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description Valve calculation API Router
*/

use \Boot\Support\Facades\Router;
use \Project\UI\API\Controllers\Calculation\ValveCalculationController;

Router::mount('/project/calculation/valve', function () {
    Router::post('/',  [ValveCalculationController::class, 'calculate'])->name('calculate');     
}, 'project.calculation.valve');
