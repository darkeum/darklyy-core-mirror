<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyChatMessageRead extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_message_read', function (Blueprint $table) {
            $table->foreign('message_id')->references('id')->on('chat_message')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::table('chat_message_read', function (Blueprint $table) {
            $table->dropForeign(['message_id']);
            $table->dropForeign(['uid']);
        });
    }
}
