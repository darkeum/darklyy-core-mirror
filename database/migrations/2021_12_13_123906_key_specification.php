<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeySpecification extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specification', function (Blueprint $table) {
            $table->foreign('uid')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('oid')->references('id')->on('organizations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('object_id')->references('id')->on('object')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('specification', function (Blueprint $table) {
            $table->dropForeign(['uid']);
            $table->dropForeign(['oid']);
            $table->dropForeign(['object_id']);
        });
    }
}
