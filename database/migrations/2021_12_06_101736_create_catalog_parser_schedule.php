<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateCatalogParserSchedule extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_parser_schedule', function (Blueprint $table) {
            $table->id();
            $table->text('when_parse')->default('1 day')->comment('Когда запрашивать');
            $table->integer('categories_id')->comment('Категория');
            $table->integer('uid');

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
        Schema::dropIfExists('catalog_parser_schedule');
    }
}
