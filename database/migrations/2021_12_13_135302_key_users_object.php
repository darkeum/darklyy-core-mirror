<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyUsersObject extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_object', function (Blueprint $table) {
            $table->foreign('oid')->references('id')->on('organizations')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('object')->references('id')->on('object')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_object', function (Blueprint $table) {
            $table->dropForeign(['oid']);
            $table->dropForeign(['object']);
        });
    }
}
