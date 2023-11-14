<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class RebaseCatalogPriceTemp extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catalog_price_temp', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->renameColumn('cid', 'id');
            $table->primary('id');
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
            $table->dropPrimary('id');
            $table->renameColumn('id', 'cid');
        });
        Schema::table('catalog_price_temp', function (Blueprint $table) {
            $table->id()->first();
        });
    }
}
