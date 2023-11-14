<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeySalesDetailStatusLog extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_detail_status_log', function (Blueprint $table) {
            $table->foreign('sales_detail_id')->references('id')->on('sales_detail')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('uid')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_detail_status_log', function (Blueprint $table) {
            $table->dropForeign(['sales_detail_id']);
            $table->dropForeign(['uid']);
        });
    }
}
