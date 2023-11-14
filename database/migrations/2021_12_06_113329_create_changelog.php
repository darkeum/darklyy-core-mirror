<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateChangelog extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('changelog', function (Blueprint $table) {
            $table->id();
            $table->integer('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('uid')->nullable()->comment('Идентификатор пользователя');
            $table->integer('category')->nullable()->comment('Идентификатор категории ');
            $table->dateTime('published_at')->useCurrent()->comment('Дата публикации');
            $table->string('title', 256)->nullable()->comment('Заголовок');
            $table->text('text')->nullable()->comment('Содержание');
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
        Schema::dropIfExists('changelog');
    }
}
