<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeySync extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync', function (Blueprint $table) {
            $table->foreign('sync_base')->references('id')->on('sync_base')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('uid')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sync', function (Blueprint $table) {
            $table->dropForeign(['sync_base']);
            $table->dropForeign(['uid']);
        });
    }
}
