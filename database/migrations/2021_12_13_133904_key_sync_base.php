<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeySyncBase extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_base', function (Blueprint $table) {
            $table->foreign('uid')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
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
        Schema::table('sync_base', function (Blueprint $table) {
            $table->dropForeign(['uid']);
            $table->dropForeign(['org']);
        });
    }
}
