<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSyncBase extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_base', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Дата удаления');
            $table->integer('uid')->nullable()->comment('Идентификатор пользователя');
            $table->string('title', 1024)->nullable()->comment('Название');
            $table->integer('org')->nullable()->comment('Идентификатор организации');
            $table->integer('type')->nullable()->comment('Тип синхронизации');
            $table->string('host', 256)->nullable()->comment('Хост');
            $table->string('user', 256)->nullable()->comment('Пользователь');
            $table->string('pass', 256)->nullable()->comment('Пароль');
            $table->dateTime('date_add')->nullable()->comment('Дата создания');
            $table->dateTime('date_send')->nullable()->comment('Дата последней отправки');
            $table->dateTime('date_get')->nullable()->comment('Дата последнего получения данных');
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
        Schema::dropIfExists('sync_base');
    }
}
