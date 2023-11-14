<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeySupplyOrderDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply_order_detail', function (Blueprint $table) {
            $table->foreign('cid')->references('id')->on('catalog')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('supply_o')->references('id')->on('supply_order')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('sale_detail_id')->references('id')->on('sales_detail')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supply_order_detail', function (Blueprint $table) {
            $table->dropForeign(['cid']);
            $table->dropForeign(['supply_o']);
            $table->dropForeign(['sale_detail_id']);
        });
    }
}
