<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyCatalogChangeLog extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_change_log', function (Blueprint $table) {
            $table->foreign('cid')->references('id')->on('catalog')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('uid')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog_change_log', function (Blueprint $table) {
            $table->dropForeign(['cid']);
            $table->dropForeign(['uid']);
            
        });
    }
}
