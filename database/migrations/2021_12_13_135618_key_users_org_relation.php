<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyUsersOrgRelation extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_org_relation', function (Blueprint $table) {
            $table->unique(['org', 'uid']);
            $table->foreign('uid')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('org')->references('id')->on('org')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_org_relation', function (Blueprint $table) {
            $table->dropIndex(['uid', 'org']);
            $table->dropForeign(['uid']);
            $table->dropForeign(['org']);
        });
    }
}
