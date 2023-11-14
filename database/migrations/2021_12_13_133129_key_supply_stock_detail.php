<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeySupplyStockDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply_stock_detail', function (Blueprint $table) {
            $table->foreign('ssid')->references('id')->on('supply_stock')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supply_stock_detail', function (Blueprint $table) {
            $table->dropForeign(['ssid']);
        });
    }
}
