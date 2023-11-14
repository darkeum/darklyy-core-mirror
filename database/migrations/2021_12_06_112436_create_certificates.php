<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCertificates extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->string('category', 255)->nullable()->comment('Идентификатор ответственного');
            $table->string('slug', 1024)->comment('Ссылка на сертификат');
            $table->string('title', 1024)->comment('Заголовок');
            $table->string('preview', 1024)->nullable()->comment('Изображения сертификата');
            $table->string('pdf', 1024)->nullable()->comment('PDF для скачивания');
            $table->string('org_name', 1024)->nullable()->comment('Кем выдан');
            $table->text('short')->nullable()->comment('Краткое описание');
            $table->text('full')->nullable()->comment('Полное описание');
            $table->string('numb', 1024)->comment('Номер сертификата');
            $table->date('date_start')->comment('Дата выдачи сертификата');
            $table->date('date_end')->nullable()->comment('Дата окончания сертификата');
            $table->integer('uid')->comment('Идентификатор ответственного');
            $table->longText('tags');
            $table->string('fulltitle', 1024)->comment('Расширенный заголовок');
            $table->text('keywords')->comment('Ключевые слова');
            $table->text('description')->comment('Описание');
            $table->integer('views')->comment('Просмотры')->default(0)->comment('Просмотры');
            $table->integer('status')->comment('Статус')->default(1)->comment('Статус');

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
        Schema::dropIfExists('certificates');
    }
}
