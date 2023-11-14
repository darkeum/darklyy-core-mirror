<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

use Boot\Models\Attach;
use Boot\System\Http\Request;
use Boot\Support\Facades\Auth;
use Boot\Support\Facades\Route;
use App\Controllers\MediaController;
use App\Controllers\LanguageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Здесь вы можете зарегистрировать веб-маршруты для своего приложения. Эти
| маршруты загружаются RouteServiceProvider в группе, которая
| содержит группу промежуточного программного обеспечения "web".
|
*/

Route::get('lang/{locale}', [LanguageController::class, 'swap']);

Route::get('admin', function () {
    return redirect('administration');
    // return redirect()->route('admin.home.dashboard');
});

// Preloader
Route::middleware(['theme:admin-material'])
    ->group(function () {

        Route::get('preloader', function (Request $request) {

            $request->class = $request->class . ' d-none';
            // $request->class = $request->class . ' d-block';

            return view('elements.preloader', ['request' => $request]);
        });

    });

Route::get('media/{media}', [MediaController::class, 'download'])->name('media-storage');

Route::group(['middleware' => ['auth', 'permission:portal access']], function () {

    if (check_company()) {
        Route::any('administration', function () {
            require_once ROOT . 'lib/loader_core.php';
        });
    }

    Route::any('grid', function () {
        $coreOnly = true;
        require_once ROOT . 'lib/loader_core.php';
        header('Access-Control-Allow-Origin: *');
        $grid_name = $_GET['jqgrid'];
        $grid_path = $_GET['patch'];
        $grid_module = $_GET['module'];
        $core->loadModLangADM($grid_module);
        loadGrid($grid_name = 'jqSimple', '', array(), $grid_path);
    });

    $component_array = array(2 => 'cats', 4 => 'comments', 5 => 'user', 6 => 'client', 11 => 'seminar', 12 => 'attribute', 13 => 'god', 15 => 'letter', 18 => 'documents', 19 => 'calendar', 20 => 'stock');
    foreach ($component_array as $key => $value) {
        Route::any('administration/' . $value . '{pp}', function (Request $request) {
            require_once ROOT . 'lib/loader_core.php';
        })->where('pp', '.*');
    }

    $services_array = array(0 => 'fastCart', 1 => 'ajax');
    foreach ($services_array as $key => $value) {
        Route::any('/administration/' . $value . '{pp}', function (Request $request) {
            require_once ROOT . 'lib/loader_core.php';
        })->where('pp', '.*');
    }


    $oldModules = ['sales', 'task', 'boiler', 'access', 'calculation', 'catalog_v2', 'catalog_old', 'content', 'converter', 'corrections', 'daemon', 'doc', 'documentation', 'economic', 'engineering', 'favorite', 'fcontrol', 'feed', 'form', 'guestbook', 'knowledge', 'logistics', 'merging', 'pto', 'realization', 'request', 'search', 'selection', 'sitemap', 'specification', 'storage', 'supply', 'sync', 'tagcode', 'verification'];

    foreach ($oldModules as $key => $value) {
        Route::any('administration/module/' . $value, function (Request $request) {
            require_once ROOT . 'lib/loader_core.php';
        });
        Route::any('administration/module/' . $value . '/{pp}', function (Request $request) {
            require_once ROOT . 'lib/loader_core.php';
        })->where('pp', '.*');
    }
});


Route::any('ajax.php', function (Request $request) {
    require_once ROOT . 'lib/loader_ajax.php';
});


Route::any('catalog_v2{pp}', function (Request $request) {
    require_once ROOT . 'lib/loader_core.php';
})->where('pp', '.*');

Route::any('content{pp}', function (Request $request) {
    require_once ROOT . 'lib/loader_core.php';
})->where('pp', '.*');

Route::any('pto{pp}', function (Request $request) {
    require_once ROOT . 'lib/loader_core.php';
})->where('pp', '.*');

Route::any('api{pp}', function (Request $request) {
    header('Access-Control-Allow-Origin: *');
    header("Content-Type: application/json");
    require_once ROOT . 'lib/loader_api.php';
})->where('pp', '.*');

Route::any('vkbot', function (Request $request) {
    $coreOnly = true;
    $old = true;
    require_once ROOT . 'lib/loader_core.php';
    require_once ROOT . 'api/vk/index-bot.php';
})->where('pp', '.*');

Route::get('download/{id}', function (Request $request, $id) {
    $attach = new Attach;
    $rows = $attach->find($id)->toArray();
    if (count($rows) > 0) {
        if ($rows['access'] != 1) {
            if (Auth::check()) {
                if (Auth::user()->can('portal access')) {
                    get_index_file($rows);
                }
            } else {
                abort('403');
            }
        } else {
            get_index_file($rows);
        }
    } else {
        abort('404');
    }
});

Route::any('victoria', function (Request $request) {
    require_once ROOT . 'lib/loader_victoria.php';
})->where('pp', '.*');
