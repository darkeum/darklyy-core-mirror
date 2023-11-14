<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateChatMessage extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_message', function (Blueprint $table) {
            $table->id();
            $table->integer('chat_id');
            $table->integer('uid')->comment('кто написал');
            $table->text('message')->nullable();
            $table->integer('backplate')->default(1)->comment('Задний фон: 1 - есть, 0 - нет');
            $table->dateTime('date');
            $table->integer('file')->default(0);
            $table->integer('active')->default(1)->comment('Видимость сообщения 1 - видимо, 0 - нет');
            $table->integer('reply')->nullable()->comment('Ответ на сообщение');

            $table->timestamps();
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_message');
    }
}
