<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyStockTransferDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfer_detail', function (Blueprint $table) {
            $table->foreign('transfer_id')->references('id')->on('stock_transfer')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_transfer_detail', function (Blueprint $table) {
            $table->dropForeign(['transfer_id']);
        });
    }
}
