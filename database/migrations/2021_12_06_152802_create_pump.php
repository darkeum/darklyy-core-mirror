<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreatePump extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pump', function (Blueprint $table) {
            $table->id();
            $table->integer('cid');
            $table->integer('uid');
            $table->integer('uid_change');
            $table->text('date')->default('[]')->comment('Данные');
            $table->integer('type_of_instalation')->default(0)->comment('Тип установки');
            $table->integer('work_line')->default(0)->comment('Рабочая линия');
            $table->integer('priority')->default(1);

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
        Schema::dropIfExists('pump');
    }
}
