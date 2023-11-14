<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyDocumentationDetailComment extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentation_detail_comment', function (Blueprint $table) {
            $table->foreign('ddid')->references('id')->on('documentation_detail')->onUpdate('restrict')->onDelete('cascade');
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
        Schema::table('documentation_detail_comment', function (Blueprint $table) {
            $table->dropForeign(['ddid']);
            $table->dropForeign(['uid']);
        });
    }
}
