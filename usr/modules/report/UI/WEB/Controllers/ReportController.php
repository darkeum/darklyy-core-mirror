<?php

namespace Report\UI\WEB\Controllers;

use \Boot\Controller\AbstractController;
use \Boot\Services\Request;

class ReportController extends AbstractController{
    static function index(){
        $items = [
            \Report\ReportGoodsMovement::class,
            \Report\ReportGoodsMovementNoTrans::class,
            \Report\ReportClientBase::class,
            \Report\ReportClientStatus::class,
            \Report\ReportRealizationGoods::class,
            \Report\ReportStorageSum::class,
            \Report\ReportStorageSumDate::class,
            \Report\ReportAverageDelivery::class,
            \Report\ReportRealization::class,
            \Report\ReportMerging::class,
        ];
        $prepare_items = [];
        foreach($items as $item){
            $prepare_items[] = [
                "router" => router("admin.report.".$item::getReportName().".index"),
                "title" => $item::getTitle(),
                "description" => $item::getDescription(),
            ];
        }
        display("@report/manager/main.html.twig", [
            "items" => $prepare_items,
        ]);
    }
}