<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCatalogPriceTemp extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_price_temp', function (Blueprint $table) {
            $table->id();
            $table->integer('cid');
            $table->integer('price_1')->nullable();
            $table->integer('price_type_1')->nullable();
            $table->integer('price_3')->nullable();
            $table->integer('price_type_3')->nullable();

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
        Schema::dropIfExists('catalog_price_temp');
    }
}
