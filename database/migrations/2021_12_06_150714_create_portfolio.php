<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreatePortfolio extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolio', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('uid')->comment('Идентификатор пользователя');
            $table->integer('category')->comment('Категория');
            $table->string('title', 1024)->comment('Названия объекта');
            $table->string('subtitle', 1024)->nullable()->comment('Подзаголовок');
            $table->string('slug', 1024)->comment('URL объекта');
            $table->text('description')->nullable()->comment('Описание объекта');
            $table->text('full')->nullable()->comment('Развернутое описание');
            $table->longText('preview')->nullable()->comment('Превью');
            $table->longText('gallery')->nullable()->comment('Галерея');
            $table->string('address', 1024)->nullable()->comment('Адрес объекта');
            $table->string('coord', 1024)->nullable()->comment('Координаты объекта');
            $table->longText('media')->nullable()->comment('Ссылки на видео');
            $table->longText('tags')->nullable();
            $table->dateTime('published_at')->useCurrent()->comment('Дата публикации');
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
        Schema::dropIfExists('portfolio');
    }
}
