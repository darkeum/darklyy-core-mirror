<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateReviews extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('uid')->comment('Отвественный');
            $table->integer('oid')->comment('Контрагент');
            $table->string('category', 1024)->comment('Категории');
            $table->integer('type')->comment('Тип: 1-письмо, 2-аудио, 3-видео');
            $table->string('slug', 1024)->comment('Ссылка');
            $table->string('title', 1024)->comment('Заголовок отзыва');
            $table->string('subtitle', 1024)->nullable()->comment('Подзаголовок');
            $table->text('content')->comment('Содержание отзыва');
            $table->longText('preview')->nullable()->comment('Изображения отзыва');
            $table->string('pdf', 1024)->nullable()->comment('Ссылка на PDF');
            $table->string('city', 1024)->nullable()->comment('Город');
            $table->date('date_coop')->nullable()->comment('Дата начала сотрудничества');
            $table->string('tags', 1024)->nullable()->comment('Теги');
            $table->string('fulltitle', 1024)->nullable()->comment('Расширенный заголовок');
            $table->string('description', 1024)->nullable()->comment('Описание');
            $table->string('keywords', 1024)->nullable()->comment('Ключевые слова');
            $table->integer('views')->default(0)->comment('Количество просмотров');
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
        Schema::dropIfExists('reviews');
    }
}
