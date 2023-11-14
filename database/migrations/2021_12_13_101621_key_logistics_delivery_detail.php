<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyLogisticsDeliveryDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logistics_delivery_detail', function (Blueprint $table) {
            $table->foreign('logistics_delivery_id')->references('id')->on('logistics_delivery')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logistics_delivery_detail', function (Blueprint $table) {
            $table->dropForeign(['logistics_delivery_id']);
        });
    }
}
