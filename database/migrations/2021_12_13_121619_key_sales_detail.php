<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeySalesDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_detail', function (Blueprint $table) {
            $table->foreign('sales_id')->references('id')->on('sales')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('cid')->references('id')->on('catalog')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('s_unit')->references('id')->on('catalog_unit')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_detail', function (Blueprint $table) {
            $table->dropForeign(['sales_id']);
            $table->dropForeign(['cid']);
            $table->dropForeign(['s_unit']);
        });
    }
}
