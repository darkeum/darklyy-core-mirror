<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyAccessUsers extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::table('access_users', function (Blueprint $table) {
            $table->foreign('uid')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('access_id')->references('id')->on('access')->onDelete('cascade')->onUpdate('cascade');                        
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('access_users', function (Blueprint $table) {
            $table->dropForeign(['uid']);
            $table->dropForeign(['access_id']);
        });
    }
}
