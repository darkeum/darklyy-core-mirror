<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

use Boot\System\Http\Request;
use Boot\Support\Facades\Route;
use Boot\Support\Facades\Config;
use Boot\Controller\TelegramApiService;
use Scada\UI\API\Controllers\ScadaClientController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Здесь вы можете зарегистрировать маршруты API для своего приложения. Эти
| маршруты загружаются RouteServiceProvider в группе, которая
| назначается группа промежуточного программного обеспечения "api". 
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::name('api.services.')->prefix('/services')->group(
    function () {
        Route::any('/telegram/'.Config::get('services.telegram-bot-api.token'),  [TelegramApiService::class, 'getUpdates'])->name('telegram');      
    }
);

Route::get('/', function (Request $request) {
   dd('API');
});


Route::name('scada.')->prefix('/socket.io')->group(
    function () {
      Route::get('/',  [ScadaClientController::class, 'settings'])->name('client.socket');
    }
  );
