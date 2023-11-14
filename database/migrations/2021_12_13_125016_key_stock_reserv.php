<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyStockReserv extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_reserv', function (Blueprint $table) {
            $table->foreign('cid')->references('id')->on('catalog')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('stock_id')->references('id')->on('stock')->onUpdate('restrict')->onDelete('restrict');
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
        Schema::table('stock_reserv', function (Blueprint $table) {
            $table->dropForeign(['cid']);
            $table->dropForeign(['stock_id']);
            $table->dropForeign(['uid']);
        });
    }
}
