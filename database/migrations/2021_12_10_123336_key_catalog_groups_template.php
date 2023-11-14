<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyCatalogGroupsTemplate extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_groups_template', function (Blueprint $table) {
            $table->foreign('catalog_groups_template_name_id')->references('id')->on('catalog_groups_template_name')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('catalog_groups_discount_id')->references('id')->on('catalog_groups_discount')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog_groups_template', function (Blueprint $table) {
            $table->dropForeign(['catalog_groups_template_name_id']);
            $table->dropForeign(['catalog_groups_discount_id']);
        });
    }
}
