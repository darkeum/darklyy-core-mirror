<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCatalogPriceRivalDiscount extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_price_rival_discount', function (Blueprint $table) {
            $table->id();
            $table->integer('cid');
            $table->double('discount');
            $table->string('host', 1024);

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
        Schema::dropIfExists('catalog_price_rival_discount');
    }
}
