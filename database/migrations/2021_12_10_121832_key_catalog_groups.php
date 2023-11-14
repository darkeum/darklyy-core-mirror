<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyCatalogGroups extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_groups', function (Blueprint $table) {
            $table->foreign('category')->references('id')->on('categories')->onUpdate('restrict')->onDelete('restrict');
            $table->unique('name'); 
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog_groups', function (Blueprint $table) {
            $table->dropForeign(['category']);
            $table->dropUnique(['name']);
        });
    }
}
