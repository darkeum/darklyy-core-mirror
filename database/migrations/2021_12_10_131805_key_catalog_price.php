<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyCatalogPrice extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_price', function (Blueprint $table) {
            $table->foreign('price_type')->references('id')->on('catalog_price_type')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('uid')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->index('active');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog_price', function (Blueprint $table) {
            $table->dropForeign(['price_type']);
            $table->dropForeign(['uid']);
            $table->dropIndex(['active']);
        });
    }
}
