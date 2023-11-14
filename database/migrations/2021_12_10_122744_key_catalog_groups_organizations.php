<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyCatalogGroupsOrganizations extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_groups_organizations', function (Blueprint $table) {
            $table->foreign('catalog_groups_discount_id')->references('id')->on('catalog_groups_discount')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('oid')->references('id')->on('organizations')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog_groups_organizations', function (Blueprint $table) {
            $table->dropForeign(['catalog_groups_discount_id']);
            $table->dropForeign(['oid']);
        });
    }
}
