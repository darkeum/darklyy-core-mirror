<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyCatalogPriceTemp extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_price_temp', function (Blueprint $table) {
            $table->foreign('cid')->references('id')->on('catalog')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('price_1')->references('id')->on('catalog_price_detail')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('price_type_1')->references('id')->on('catalog_price_type')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('price_3')->references('id')->on('catalog_price_detail')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('price_type_3')->references('id')->on('catalog_price_type')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog_price_temp', function (Blueprint $table) {
            $table->dropForeign(['cid']);
            $table->dropForeign(['price_1']);
            $table->dropForeign(['price_type_1']);
            $table->dropForeign(['price_3']);
            $table->dropForeign(['price_type_3']);
        });
    }
}
