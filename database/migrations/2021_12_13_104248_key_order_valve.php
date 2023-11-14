<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyOrderValve extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_valve', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('order')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_valve', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });
    }
}
