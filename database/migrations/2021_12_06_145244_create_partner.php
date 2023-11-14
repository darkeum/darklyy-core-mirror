<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreatePartner extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('uid');
            $table->integer('category')->nullable();
            $table->string('title', 1024)->nullable()->comment('Заголовок');
            $table->text('short')->nullable()->comment('Краткое содержание');
            $table->text('full')->nullable()->comment('Полное содержание');
            $table->string('site', 1024)->nullable();
            $table->string('site_url', 1024)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 255)->nullable();
            $table->string('location', 255)->nullable();
            $table->string('address', 1024)->nullable();
            $table->string('page', 11)->nullable();
            $table->string('slug', 55)->nullable();
            $table->text('preview')->nullable();
            $table->string('preview_mini', 1024)->nullable();
            $table->string('main_image', 1024)->nullable();
            $table->string('fulltitle', 1024)->nullable();
            $table->text('keywords')->nullable()->comment('Ключевые слова');
            $table->text('description')->nullable()->comment('Описание');
            $table->integer('views')->nullable()->default(1)->comment('Количество просмотров');
            $table->string('color_class', 1024)->nullable()->comment('Класс');
            $table->integer('status')->nullable()->default(1)->comment('Статус');
            $table->dateTime('published_at')->nullable()->useCurrent()->comment('Дата публикации');

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
        Schema::dropIfExists('partner');
    }
}
