<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyCatalogPriceRivalDiscount extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_price_rival_discount', function (Blueprint $table) {
            $table->foreign('cid')->references('id')->on('catalog')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog_price_rival_discount', function (Blueprint $table) {
            $table->dropForeign(['cid']);
        });
    }
}
