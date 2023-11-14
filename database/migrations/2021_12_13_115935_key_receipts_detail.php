<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyReceiptsDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts_detail', function (Blueprint $table) {
            $table->foreign('rid')->references('id')->on('receipts')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('cid')->references('id')->on('catalog')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('supply_detail_id')->references('id')->on('supply_order_detail')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receipts_detail', function (Blueprint $table) {
            $table->dropForeign(['rid']);
            $table->dropForeign(['cid']);
            $table->dropForeign(['supply_detail_id']);
        });
    }
}
