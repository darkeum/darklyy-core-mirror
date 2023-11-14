<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyDocumentationDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentation_detail', function (Blueprint $table) {
            $table->foreign('did')->references('id')->on('documentation')->onUpdate('restrict')->onDelete('cascade');
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
        Schema::table('correct_detail', function (Blueprint $table) {
            $table->dropForeign(['did']);
            $table->dropForeign(['uid']);
        });
    }
}
