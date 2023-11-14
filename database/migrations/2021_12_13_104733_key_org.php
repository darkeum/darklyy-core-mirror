<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyOrg extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('org', function (Blueprint $table) {
            $table->unique('prefix');
            $table->foreign('oid')->references('id')->on('organizations')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('director')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('org', function (Blueprint $table) {
            $table->dropUnique(['prefix']);
            $table->dropForeign(['oid']);
            $table->dropForeign(['director']);
        });
    }
}
