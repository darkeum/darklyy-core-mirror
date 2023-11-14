<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyBoilerBidDataDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boiler_bid_data_detail', function (Blueprint $table) {
            $table->foreign('bbd_id')->references('id')->on('boiler_bid_data')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('cid')->references('id')->on('catalog')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boiler_bid_data_detail', function (Blueprint $table) {
            $table->dropForeign(['bbd_id']);
            $table->dropForeign(['cid']);
        });
    }
}
