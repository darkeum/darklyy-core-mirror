<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyChatMessage extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_message', function (Blueprint $table) {
            $table->foreign('chat_id')->references('id')->on('chat')->onUpdate('restrict')->onDelete('restrict');
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
        Schema::table('catalog_price_type', function (Blueprint $table) {
            $table->dropForeign(['chat_id']);
            $table->dropForeign(['uid']);
        });
    }
}
