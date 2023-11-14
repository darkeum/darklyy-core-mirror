<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeySupplyDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply_detail', function (Blueprint $table) {
            $table->foreign('sid')->references('id')->on('supply')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('cid')->references('id')->on('catalog')->onUpdate('restrict')->onDelete('restrict');
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
        Schema::table('supply_detail', function (Blueprint $table) {
            $table->dropForeign(['sid']);
            $table->dropForeign(['cid']);
            $table->dropForeign(['sdid']);
        });
    }
}
