<?php

use Boot\Support\Facades\Route;
use Slider\UI\WEB\Controllers\ManagerController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'auth'])
    ->prefix('/administration/module/slider')
    ->name('admin.')
    ->group(function () {

        //
        Route::resource('/', ManagerController::class)->names('slider')->parameters(['' => 'id']);

    });
