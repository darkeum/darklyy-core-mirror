<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCatalogPriceType extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_price_type', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable()->comment('Названия вида цены');
            $table->integer('currency')->nullable()->comment('Валюта');
            $table->integer('parent')->nullable();
            $table->double('course');
            $table->integer('hidden')->default(0);

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
        Schema::dropIfExists('catalog_price_type');
    }
}
