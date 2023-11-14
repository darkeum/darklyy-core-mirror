<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

use Boot\Support\Facades\Route;
use Developers\UI\WEB\Controllers\Controller;
use Developers\UI\WEB\Controllers\GitController;
use Developers\UI\WEB\Controllers\DaemonController;
use Developers\UI\WEB\Controllers\ComposerController;
use Developers\UI\WEB\Controllers\WiloController;

Route::middleware(['theme:admin-material', 'permission:portal access', 'permission:developers access', 'auth'])
    ->prefix('administration/module/developers')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [Controller::class, 'index'])->name('developers.index');

        // GIT
        Route::get('git', [GitController::class, 'index'])->name('developers.git');

        // COMPOSER
        Route::get('composer', [ComposerController::class, 'index'])->name('developers.composer');

        // DAEMON
        Route::resource('daemon', DaemonController::class)->names('developers.daemon')->parameters(['' => 'id']);
        Route::post('startdaemon', [DaemonController::class, 'startDaemon'])->name('developers.startdaemon');
        Route::post('stopdaemon', [DaemonController::class, 'stopDaemon'])->name('developers.stopdaemon');

        // ROUTES - Вывод всех роутов сайта
        Route::get('routes', function () {
            return view('developers::manager.routes', ['routes' => Route::getRoutes()]);
        })->name('developers.routes');

        // Парсер Wilo
        Route::get('wilo', [WiloController::class, 'index'])->name('developers.wilo');
        Route::post('wilo', [WiloController::class, 'loader']);

    });

// РЕДИРЕКТЫ
Route::middleware(['theme:admin', 'permission:portal access', 'permission:developers access', 'auth'])
    ->prefix('administration/module/dev')
    ->name('admin.')
    ->group(function () {

        // ГЛАВНАЯ
        Route::get('/', function () {
            return redirect()->route('admin.developers.index');
        })->name('dev.index');

        // GIT
        Route::get('git', function () {
            return redirect()->route('admin.developers.git');
        })->name('dev.git');

        // COMPOSER
        Route::get('composer', function () {
            return redirect()->route('admin.developers.composer');
        })->name('dev.composer');

    });
