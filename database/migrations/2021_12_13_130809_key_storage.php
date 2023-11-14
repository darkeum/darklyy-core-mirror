<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyStorage extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage', function (Blueprint $table) {
            $table->foreign('uid')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('oid')->references('id')->on('organizations')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('object')->references('id')->on('object')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('stock_id')->references('id')->on('stock')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('sales_id')->references('id')->on('sales')->onUpdate('restrict')->onDelete('restrict');
            $table->index('supply_id');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('storage', function (Blueprint $table) {
            $table->dropForeign(['uid']);
            $table->dropForeign(['oid']);
            $table->dropForeign(['object']);
            $table->dropForeign(['stock_id']);
            $table->dropForeign(['sales_id']);
            $table->dropIndex('supply_id');
        });
    }
}
