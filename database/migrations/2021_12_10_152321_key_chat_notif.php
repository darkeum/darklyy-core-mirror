<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyChatNotif extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_notif', function (Blueprint $table) {
            $table->foreign('chat_id')->references('id')->on('chat')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('uid')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_message_read', function (Blueprint $table) {
            $table->dropForeign(['chat_id']);
            $table->dropForeign(['uid']);
        });
    }
}
