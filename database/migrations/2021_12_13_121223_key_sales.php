<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeySales extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->foreign('org')->references('id')->on('org')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('stock_id')->references('id')->on('stock')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('oid')->references('id')->on('organizations')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('object')->references('id')->on('object')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('uid')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('task_id')->references('id')->on('task')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['org']);
            $table->dropForeign(['stock_id']);
            $table->dropForeign(['oid']);
            $table->dropForeign(['object']);
            $table->dropForeign(['uid']);
            $table->dropForeign(['task_id']);
        });
    }
}
