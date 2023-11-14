<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyCatalog extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog', function (Blueprint $table) {
            $table->foreign('catalog_groups_id')->references('id')->on('catalog_groups')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('man')->references('id')->on('catalog_manufacture')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('unit')->references('unit')->on('catalog_unit')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('types')->references('id')->on('catalog_types')->onUpdate('restrict')->onDelete('restrict');
            $table->index('title'); 
            $table->index('hidden'); 
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog', function (Blueprint $table) {
            $table->dropForeign(['catalog_groups_id']);
            $table->dropForeign(['man']);
            $table->dropForeign(['unit']);
            $table->dropForeign(['types']);
            $table->dropIndex('title'); 
            $table->dropIndex('hidden');
        });
    }
}
