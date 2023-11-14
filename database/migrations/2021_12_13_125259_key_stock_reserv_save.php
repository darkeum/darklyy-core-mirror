<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyStockReservSave extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_reserv_save', function (Blueprint $table) {
            $table->foreign('receipts_detail_id')->references('id')->on('receipts_detail')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_reserv_save', function (Blueprint $table) {
            $table->dropForeign(['receipts_detail_id']);
        });
    }
}
