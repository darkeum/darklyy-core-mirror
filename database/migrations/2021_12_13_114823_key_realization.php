<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyRealization extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realization', function (Blueprint $table) {
            $table->foreign('stock_id')->references('id')->on('stock')->onUpdate('cascade')->onDelete('cascade');
            $table->index('sales_id');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('realization', function (Blueprint $table) {
            $table->dropForeign(['stock_id']);
            $table->dropIndex(['sales_id']);
        });
    }
}
