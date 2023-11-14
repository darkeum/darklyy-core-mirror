<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyControlDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('control_detail', function (Blueprint $table) {
            $table->foreign('control_id')->references('id')->on('control')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('sod_id')->references('id')->on('supply_order_detail')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('sdid')->references('id')->on('sales_detail')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('control_detail', function (Blueprint $table) {
            $table->dropForeign(['control_id']);
            $table->dropForeign(['sod_id']);
            $table->dropForeign(['sdid']);
        });
    }
}
