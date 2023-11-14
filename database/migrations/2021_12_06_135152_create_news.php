<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateNews extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->integer('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('uid')->comment('Идентификатор пользователя');
            $table->integer('category')->nullable()->comment('Идентификатор категории');
            $table->dateTime('published_at')->useCurrent()->comment('Дата новости');
            $table->longText('tags')->nullable();
            $table->string('slug', 1024);
            $table->longText('preview')->nullable();
            $table->string('title', 1024)->nullable();
            $table->text('short')->nullable();
            $table->text('full')->nullable();
            $table->string('fulltitle', 1024)->nullable();
            $table->text('keywords')->nullable();
            $table->text('description')->nullable();
            $table->smallInteger('views')->default(0);
            $table->integer('favorite')->default(0);
            $table->integer('status')->default(1);

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
        Schema::dropIfExists('news');
    }
}
