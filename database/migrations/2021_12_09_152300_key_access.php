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
        Schema::table('access', function (Blueprint $table) {
            $table->foreign('uid')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('category')->references('id')->on('category')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('org')->references('id')->on('org')->onDelete('restrict')->onUpdate('restrict');                        
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('access', function (Blueprint $table) {
            $table->dropForeign(['uid']);
            $table->dropForeign(['category']);
            $table->dropForeign(['org']);
        });
    }
}
