<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCommand extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('command', function (Blueprint $table) {
            $table->id();
            $table->string('numb', 255)->nullable();
            $table->integer('numb_counter')->nullable();
            $table->integer('uid')->nullable();
            $table->integer('org');
            $table->text('comment')->nullable();
            $table->dateTime('date')->nullable();
            $table->dateTime('date_end')->nullable()->comment('дата по какое');
            $table->dateTime('date_create')->nullable()->useCurrent();
            $table->integer('type')->default()->comment('Тип приказа');
            $table->integer('file')->nullable()->comment('id файла word');
            $table->integer('scan')->nullable()->comment('id файла скана pdf');
            $table->text('worker')->nullable()->default('[]')->comment('Сотрудники');
            $table->text('data')->default('{}')->comment('JSON. Для доп. данных');

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
        Schema::dropIfExists('command');
    }
}
