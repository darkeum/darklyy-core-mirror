<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateJob extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('uid')->comment('Идентификатор пользователя');
            $table->integer('category')->comment('Идентификатор категории');
            $table->string('title', 1024)->comment('Заголовок вакансии');
            $table->string('slug', 1024)->comment('Ссылка на вакансию');
            $table->text('description')->nullable()->comment('Описание вакансии');
            $table->text('duties')->nullable()->comment('Список обязанностей');
            $table->text('conditions')->nullable()->comment('Список условий');
            $table->text('requirements')->nullable()->comment('Список требований');
            $table->integer('responsible')->nullable()->comment('Идентификатор руководителя');
            $table->string('salary', 1024)->nullable()->comment('Информация по зарплате');
            $table->integer('status')->default(1)->comment('Статус');

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
        Schema::dropIfExists('job');
    }
}
