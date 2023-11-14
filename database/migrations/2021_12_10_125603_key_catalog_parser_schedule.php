<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyCatalogParserSchedule extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_parser_schedule', function (Blueprint $table) {
            $table->foreign('uid')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('categories_id')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog_parser_schedule', function (Blueprint $table) {
            $table->dropForeign(['uid']);
            $table->dropForeign(['categories_id']);
        });
    }
}
