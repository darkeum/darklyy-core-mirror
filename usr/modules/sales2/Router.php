<?php

use \Boot\Support\Facades\Route;
use \Boot\Support\Facades\Router;
use Sales2\Controller\SalesController;
use Sales2\Controller\SalesApiController;

Router::mount('/administration/module/sales2', function (){ 
    Router::mount('/ajax', function (){ 
        Router::get('/status_log/{id}',  [SalesApiController::class, "status_log"])->name("status_log");
        Router::get('/reserve/action/{id}',  [SalesApiController::class, "reserve_action"])->name("reserve.action");
        Router::post('/reserve/create',  [SalesApiController::class, "reserve_create"])->name("reserve.create");
        Router::post('/reserve/delete',  [SalesApiController::class, "reserve_delete"])->name("reserve.delete");
        Router::post('/discount_oid',  [SalesApiController::class, "discount_oid"])->name("discount_oid");
        Router::post('/fast_change_sales',  [SalesApiController::class, "fast_change_sales"])->name("fast_change_sales");
        Router::post('/change_sales_form/{id}',  [SalesApiController::class, "change_sales_form"])->name("change_sales_form");
        Router::get('/sales_check/{sales_id}',  [SalesApiController::class, "sales_check"])->name("sales_check");
        Router::post('/actualize_create',  [SalesApiController::class, "actualize_create"])->name("actualize_create");
        Router::post('/send_purchase',  [SalesApiController::class, "send_purchase"])->name("send_purchase");
        Router::post('/check_can_delete',  [SalesApiController::class, "check_can_delete"])->name("check_can_delete");
        Router::post('/check_zero_sum',  [SalesApiController::class, "check_zero_sum"])->name("check_zero_sum");
        Router::post('/realization_create',  [SalesApiController::class, "realization_create"])->name("realization_create");
        Router::get('/comments/get/{id}',  [SalesApiController::class, "comments_get"])->name("comments.get");
        Router::post('/comments/create',  [SalesApiController::class, "comments_create"])->name("comments.create");
        Router::get('/messages/get/{id}',  [SalesApiController::class, "messages_get"])->name("messages.get");
        Router::post('/messages/send',  [SalesApiController::class, "messages_send"])->name("messages.send");
        Router::get('/end_sales/middleware/{id}',  [SalesApiController::class, "end_sales_middleware"])->name("end_sales.middleware");
        Router::post('/end_sales',  [SalesApiController::class, "end_sales"])->name("end_sales");
        Router::get('/check_all_realization_and_transfer/{id}',  [SalesApiController::class, "check_all_realization_and_transfer"])->name("check_all_realization_and_transfer");
        Router::post('/clipboard_prepare',  [SalesApiController::class, "clipboard_prepare"])->name("clipboard_prepare");
        Router::post('/extended_sort/{id}',  [SalesApiController::class, "extended_sort"])->name("extended_sort");
        Router::get('/users_list/get/{id}',  [SalesApiController::class, "users_list_get"])->name("users_list.get");
        Router::post('/users_list/create',  [SalesApiController::class, "users_list_create"])->name("users_list.create");
        Router::get('/users_list/delete/{id}',  [SalesApiController::class, "users_list_delete"])->name("users_list.delete");
        Router::post('/assembly_count',  [SalesApiController::class, "assembly_count"])->name("assembly_count");
        Router::post('/assembly_create',  [SalesApiController::class, "assembly_create"])->name("assembly_create");
        Router::post('/contract/get',  [SalesApiController::class, "contract_get"])->name("contract.get");
        Router::post('/contract/save',  [SalesApiController::class, "contract_save"])->name("contract.save");
        Router::post('/contract/create',  [SalesApiController::class, "contract_create"])->name("contract.create");
        Router::post('/contract/check',  [SalesApiController::class, "contract_check"])->name("contract.check");
        Router::post('/specification/create',  [SalesApiController::class, "specification_create"])->name("specification.create");
        Router::post('/transfer/create',  [SalesApiController::class, "transfer_create"])->name("transfer.create");
        Router::post('/load_max_discount',  [SalesApiController::class, "load_max_discount"])->name("load_max_discount");
        Router::post('/confirm_supply_order/get',  [SalesApiController::class, "confirm_supply_order_get"])->name("confirm_supply_order.get");
        Router::post('/confirm_supply_order/send',  [SalesApiController::class, "confirm_supply_order_send"])->name("confirm_supply_order.send");
        Router::post('/status_client_block/send',  [SalesApiController::class, "status_client_block_send"])->name("status_client_block.send");
        Router::get('/pay/get/{id}',  [SalesApiController::class, "pay_get"])->name("pay.get");
        Router::post('/purchase_data/get',  [SalesApiController::class, "purchase_data_get"])->name("purchase_data.get");
        Router::get('/get_realization_sales/{id}',  [SalesApiController::class, "get_realization_sales"])->name("get_realization_sales");
        Router::get('/get_transfer_sales/{id}',  [SalesApiController::class, "get_transfer_sales"])->name("get_transfer_sales");
        Router::post('/price_type_select',  [SalesApiController::class, "price_type_select"])->name("price_type_select");
        Router::post('/files/get',  [SalesApiController::class, "files_get"])->name("files.get");
        Router::post('/ndr/get',  [SalesApiController::class, "ndr_get"])->name("ndr.get");
    }, "ajax.");

    Router::post('/victoria_append_prepare',  [SalesController::class, "victoria_append_prepare"])->name("victoria_append_prepare"); 
    Router::post('/victoria_replace_prepare',  [SalesController::class, "victoria_replace_prepare"])->name("victoria_replace_prepare");
    Router::get('/victoria_append/{id}',  [SalesController::class, "victoria_append"])->name("victoria_append"); 
    Router::get('/victoria_replace/{id}',  [SalesController::class, "victoria_replace"])->name("victoria_replace"); 
    Router::post('/save',  [SalesController::class, "save"])->name("save"); 
    Router::get('/search',  [SalesController::class, "search"])->name("search.index"); 
    Router::post('/search',  [SalesController::class, "get_search"])->name("search.get"); 
    Router::get('/print/{type}/{id}',  [SalesController::class, "print"])->name("print"); 
    Router::get('/redirect_original/{id}',  [SalesController::class, "redirect_original"])->name("redirect_original"); 
    Router::resource('/',  SalesController::class); 
}, "admin.sales.");

// dd(Route::dumpAlias());