<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyPump extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pump', function (Blueprint $table) {
            $table->foreign('cid')->references('id')->on('catalog')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('uid')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('uid_change')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pump', function (Blueprint $table) {
            $table->dropForeign(['cid']);
            $table->dropForeign(['uid']);
            $table->dropForeign(['uid_change']);
        });
    }
}
