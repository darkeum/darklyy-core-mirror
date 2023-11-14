<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyStockDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_detail', function (Blueprint $table) {
            $table->foreign('stock')->references('id')->on('stock')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('cid')->references('id')->on('catalog')->onUpdate('restrict')->onDelete('restrict');
            $table->index('id_1c');
            $table->index('unique_id_2');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_detail', function (Blueprint $table) {
            $table->dropForeign(['stock']);
            $table->dropForeign(['cid']);
            $table->dropIndex(['id_1c']);
            $table->dropIndex(['unique_id_2']);
        });
    }
}
