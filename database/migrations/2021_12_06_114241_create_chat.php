<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateChat extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat', function (Blueprint $table) {
            $table->id();
            $table->string('module', 255)->nullable();
            $table->integer('pid')->nullable();
            $table->text('uid')->default('[]')->comment('JSON. Группа пользователей чата');
            $table->text('title')->comment('Название чата');
            $table->dateTime('date_create');
            $table->integer('type')->comment('Тип чата: 1 - портальный; 2 - сайт');

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
        Schema::dropIfExists('chat');
    }
}
