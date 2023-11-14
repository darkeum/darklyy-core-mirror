<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSync extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->nullable()->comment('Идентификатор ответственного');
            $table->integer('sync_base')->nullable()->comment('Идентификатор назначения синхронизации');
            $table->dateTime('date')->nullable()->comment('Дата синхронизации');
            $table->integer('count_send')->nullable()->comment('Количество отправленных элементов');
            $table->integer('count_get')->nullable()->comment('Количество полученных элементов');
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
        Schema::dropIfExists('sync');
    }
}
