<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateGuestbook extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guestbook', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->nullable()->comment('Идентификатор пользователя');
            $table->dateTime('date')->nullable()->comment('Дата');
            $table->string('name', 50)->nullable()->comment('Имя');
            $table->string('email', 50)->nullable()->comment('Почта');
            $table->string('website', 75)->nullable()->comment('Сайт');
            $table->string('phone', 256)->nullable()->comment('Телефон');
            $table->string('company', 256)->nullable()->comment('Компания');
            $table->string('post', 256)->nullable()->comment('Должность');
            $table->string('gender', 10)->nullable()->comment('Пол');
            $table->string('avatar', 256)->nullable()->comment('Аватар');
            $table->text('comment')->nullable()->comment('Комментарий');
            $table->text('reply')->nullable()->comment('Ответ');
            $table->integer('status')->nullable()->comment('Статус');

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
        Schema::dropIfExists('guestbook');
    }
}
