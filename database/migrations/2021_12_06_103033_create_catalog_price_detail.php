<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateCatalogPriceDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_price_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('price_id')->comment('Идентификатор документа');
            $table->integer('cid')->comment('Идентификатор товара');
            $table->decimal('sum')->default(0)->comment('Цена');
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
        Schema::dropIfExists('catalog_price_detail');
    }
}
