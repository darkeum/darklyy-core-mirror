<?php

use Boot\Support\Facades\Route;
use Report\Support\ReportSupport;
use Report\UI\WEB\Controllers\ReportController;

Route::middleware(['permission:portal access', 'auth'])
    ->prefix('/administration/module/report')
    ->name('admin.report.')
    ->group(function () {

        ReportSupport::router_gen(\Report\ReportGoodsMovement::class);
        ReportSupport::router_gen(\Report\ReportGoodsMovementNoTrans::class);
        ReportSupport::router_gen(\Report\ReportClientBase::class);
        ReportSupport::router_gen(\Report\ReportClientStatus::class);
        ReportSupport::router_gen(\Report\ReportRealizationGoods::class);
        ReportSupport::router_gen(\Report\ReportStorageSum::class);
        ReportSupport::router_gen(\Report\ReportStorageSumDate::class);
        ReportSupport::router_gen(\Report\ReportAverageDelivery::class);
        ReportSupport::router_gen(\Report\ReportRealization::class);
        ReportSupport::router_gen(\Report\ReportMerging::class);

        Route::get('/download_excel', [ReportSupport::class, "download_excel"])->name("download_excel");
        Route::get('/victoria_add/{index}', [ReportSupport::class, "victoria_add"])->name("victoria_add");
        Route::resource('/', ReportController::class);
    }, "admin.report.");

// dd(Router::dumpAlias());