<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCatalogPriceDiscount extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_price_discount', function (Blueprint $table) {
            $table->id();
            $table->integer('cid');
            $table->integer('type');
            $table->decimal('discount');

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
        Schema::dropIfExists('catalog_price_discount');
    }
}
