<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSyncRegister extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_register', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('uid')->nullable()->comment('Идентификатор ответственного');
            $table->string('module', 256)->nullable()->comment('Имя модуля');
            $table->integer('pid')->nullable()->comment('Идентификатор записи');
            $table->integer('type')->nullable()->comment('Тип изменений');
            $table->dateTime('date_add')->nullable()->comment('Дата изменений');
            $table->dateTime('date_sync')->nullable()->comment('Дата синхронизации');
            $table->integer('sync_base')->nullable()->comment('Назначение синхронизации');
            $table->integer('sync_id')->nullable()->comment('Идентификатор синхронизации');
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
        Schema::dropIfExists('sync_register');
    }
}
