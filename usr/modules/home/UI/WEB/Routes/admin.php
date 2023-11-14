<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description Dashboard Home WEB Router
*/

use Boot\Support\Facades\Auth;
use Boot\Support\Facades\Route;
use Home\UI\WEB\Controllers\DashboardController;

if (check_company()) {
    Route::get('administration/home', DashboardController::class)->name('admin.home.dashboard')->middleware(['theme:admin-material', 'permission:portal access', 'auth']);
} else {
    Route::get('administration', DashboardController::class)->name('admin.home.dashboard')->middleware(['theme:admin-material', 'permission:portal access', 'auth']);
}


// Временный роут - лень
Route::get('adm', function () {
    return redirect('administration/home');
});

//временно для теста
if (check_company()) {
    Route::get('/administration', function () {
        if (check_company() && in_array(Auth::id(), [1, 2, 3, 4, 13, 20, 46, 48, 79, 786, 1092,  1271])) {
            return redirect('administration/home');
        } else {
            require_once ROOT . 'lib/loader_core.php';
        }
    });
}
