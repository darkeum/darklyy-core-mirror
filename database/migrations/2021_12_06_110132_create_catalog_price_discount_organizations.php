<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCatalogPriceDiscountOrganizations extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_price_discount_organizations', function (Blueprint $table) {
            $table->id();
            $table->integer('oid');
            $table->integer('cid');
            $table->integer('type')->comment('Тип скидки');
            $table->integer('auto')->default(0);

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
        Schema::dropIfExists('catalog_price_discount_organizations');
    }
}
