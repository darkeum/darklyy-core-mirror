<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyLogisticsDriverDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logistics_driver_detail', function (Blueprint $table) {
            $table->foreign('logistics_detail_id')->references('id')->on('logistics_detail')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('driver')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logistics_driver_detail', function (Blueprint $table) {
            $table->dropForeign(['logistics_detail_id']);
            $table->dropForeign(['driver']);
        });
    }
}
