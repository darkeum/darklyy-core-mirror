<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyCorrectDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correct_detail', function (Blueprint $table) {
            $table->foreign('correct_id')->references('id')->on('correct')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('realization_detail_id')->references('id')->on('realization_detail')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('correct_detail', function (Blueprint $table) {
            $table->dropForeign(['correct_id']);
            $table->dropForeign(['realization_detail_id']);
        });
    }
}
