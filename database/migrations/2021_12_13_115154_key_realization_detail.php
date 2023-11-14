<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyRealizationDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realization_detail', function (Blueprint $table) {
            $table->foreign('rid')->references('id')->on('realization')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('realization_detail', function (Blueprint $table) {
            $table->dropForeign(['rid']);
        });
    }
}
