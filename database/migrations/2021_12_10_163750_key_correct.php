<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyCorrect extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correct', function (Blueprint $table) {
            $table->foreign('uid')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('realization_id')->references('id')->on('realization')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('org')->references('id')->on('org')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('correct', function (Blueprint $table) {
            $table->dropForeign(['uid']);
            $table->dropForeign(['realization_id']);
            $table->dropForeign(['org']);
        });
    }
}
