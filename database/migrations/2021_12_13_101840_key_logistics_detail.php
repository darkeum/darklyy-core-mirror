<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyLogisticsDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logistics_detail', function (Blueprint $table) {
            $table->foreign('sod_id')->references('id')->on('supply_order_detail')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logistics_detail', function (Blueprint $table) {
            $table->dropForeign(['sod_id']);
        });
    }
}
