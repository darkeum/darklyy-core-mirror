<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSyncRelation extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_relation', function (Blueprint $table) {
            $table->id();
            $table->string('id_1c', 36)->nullable()->comment('Ключ 1С');
            $table->string('module', 256)->nullable()->comment('Модуль');
            $table->integer('pid')->nullable()->comment('Идентификатор элемента');
            $table->integer('sync_base')->nullable()->comment('Идентификатор программы ');
            $table->dateTime('date')->nullable()->comment('Дата');
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
        Schema::dropIfExists('sync_relation');
    }
}
