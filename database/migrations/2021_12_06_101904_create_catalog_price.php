<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateCatalogPrice extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_price', function (Blueprint $table) {
            $table->id();
            $table->integer('price_type')->comment('Вид цены');
            $table->integer('uid')->comment('Идентификатор ответственного');
            $table->integer('aid')->nullable()->comment('ИД прикрепленного файла');
            $table->string('module', 255)->nullable()->comment('Родительский модуль');
            $table->integer('pid')->nullable()->comment('ИД записи в родительском модуле');
            $table->dateTime('date')->useCurrent()->comment('Дата создания');
            $table->string('comment', 1024)->comment('Комментарий');
            $table->integer('active')->default(0)->comment('Проведенность ');
            $table->integer('auto')->default(0);
            $table->integer('daemon')->default(0);
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
        Schema::dropIfExists('catalog_price');
    }
}
