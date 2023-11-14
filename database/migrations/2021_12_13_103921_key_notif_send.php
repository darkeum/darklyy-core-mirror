<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyNotifSend extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notif_send', function (Blueprint $table) {
            $table->foreign('nid')->references('id')->on('notif')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notif_send', function (Blueprint $table) {
            $table->dropForeign(['nid']);
        });
    }
}
