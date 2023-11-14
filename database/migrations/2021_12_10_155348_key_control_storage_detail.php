<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyControlStorageDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('control_storage_detail', function (Blueprint $table) {
            $table->foreign('control_storage_id')->references('id')->on('control_storage')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('storage_id')->references('id')->on('storage')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('storage_detail_id')->references('id')->on('storage_detail')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('control_storage_detail', function (Blueprint $table) {
            $table->dropForeign(['control_storage_id']);
            $table->dropForeign(['storage_id']);
            $table->dropForeign(['storage_detail_id']);
        });
    }
}
