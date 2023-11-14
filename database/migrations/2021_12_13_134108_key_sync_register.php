<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeySyncRegister extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_register', function (Blueprint $table) {
            $table->foreign('uid')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('sync_base')->references('id')->on('sync_base')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('sync_id')->references('id')->on('sync_id')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sync_register', function (Blueprint $table) {
            $table->dropForeign(['uid']);
            $table->dropForeign(['sync_base']);
            $table->dropForeign(['sync_id']);
        });
    }
}
