<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateUsersSign extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_sign', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->nullable()->comment('Идентификатор ответственного');
            $table->integer('type')->nullable()->comment('Тип: 1 - Основное ответственное лицо организации; 2 - лицо, имеющее право подписи документов');
            $table->integer('base')->nullable()->comment('На основании');
            $table->string('base_numb', 1024)->nullable()->comment('Номер ');
            $table->date('base_date')->nullable()->comment('Дата "от"');
            $table->integer('sign')->nullable()->comment('Выбранный варинат');
            $table->dateTime('date')->nullable()->comment('Дата создания');
            $table->date('date_start')->nullable()->comment('Период действия "с"');
            $table->date('date_end')->nullable()->comment('Период действия "до"');
            $table->integer('org')->nullable()->comment('Идентификатор организации');
            $table->integer('type_post')->nullable()->comment('Тип должности');
            $table->integer('status')->nullable()->default(1)->comment('Статус');

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
        Schema::dropIfExists('users_sign');
    }
}
