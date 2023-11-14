<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyCatalogManufacturePrice extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_manufacture_price', function (Blueprint $table) {
            $table->foreign('manufacture_id')->references('id')->on('catalog_manufacture')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('uid')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('price_type_id')->references('id')->on('catalog_price_type')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog_manufacture_price', function (Blueprint $table) {
            $table->dropForeign(['manufacture_id']);
            $table->dropForeign(['uid']);
            $table->dropForeign(['price_type_id']);
        });
    }
}
