<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateTask extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->default(1)->comment('Тип задачи');
            $table->string('title', 1024)->nullable()->comment('Заголовок задачи');
            $table->text('task')->nullable();
            $table->integer('fromid')->comment('Постановщик');
            $table->integer('toid')->nullable()->comment('Получатель');
            $table->text('coperformerid')->nullable()->comment('Список соисполнителей');
            $table->text('observerid')->nullable()->comment('Список наблюдателей');
            $table->text('requestid')->nullable()->comment('Список запросов на просмотр');
            $table->integer('object')->nullable();
            $table->integer('base')->nullable();
            $table->dateTime('date')->useCurrent();
            $table->integer('endsw')->default(0);
            $table->dateTime('date_end')->nullable();
            $table->integer('auto_delete')->default(0);
            $table->integer('status')->default(1);
            $table->integer('archive')->default(0);

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
        Schema::dropIfExists('task');
    }
}
