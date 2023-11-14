<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCatalogTypes extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_types', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->nullable()->comment('Идентификатор ответственного');
            $table->string('title', 256)->nullable()->comment('Заголовок вида');
            $table->integer('service')->nullable()->comment('Услуга');
            $table->integer('ofd')->nullable();
            $table->dateTime('date')->nullable()->comment('Дата создания');
            $table->integer('status')->nullable()->comment('Статус');

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
        Schema::dropIfExists('catalog_types');
    }
}
