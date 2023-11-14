<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyNotifDaemon extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notif_daemon', function (Blueprint $table) {
            $table->foreign('notif_id')->references('id')->on('notif')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notif_daemon', function (Blueprint $table) {
            $table->dropForeign(['notif_id']);
        });
    }
}
