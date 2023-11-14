<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyCatalogGroupsDiscount extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_groups_discount', function (Blueprint $table) {
            $table->foreign('catalog_groups_id')->references('id')->on('catalog_groups')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('catalog_groups_discount_name_id')->references('id')->on('catalog_groups_discount_name')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog_groups_discount', function (Blueprint $table) {
            $table->dropForeign(['catalog_groups_id']);
            $table->dropForeign(['catalog_groups_discount_name_id']);
        });
    }
}
