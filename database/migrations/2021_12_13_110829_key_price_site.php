<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyPriceSite extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_site', function (Blueprint $table) {
            $table->foreign('catalog_manufacture_id')->references('id')->on('catalog_manufacture')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('price_site', function (Blueprint $table) {
            $table->dropForeign(['catalog_manufacture_id']);
        });
    }
}
