<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyStorageDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_detail', function (Blueprint $table) {
            $table->foreign('cid')->references('id')->on('catalog')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('storage_id')->references('id')->on('storage')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('storage_detail', function (Blueprint $table) {
            $table->dropForeign(['cid']);
            $table->dropForeign(['storage_id']);
        });
    }
}
