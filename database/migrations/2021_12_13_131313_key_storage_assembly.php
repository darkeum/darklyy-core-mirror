<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyStorageAssembly extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_assembly', function (Blueprint $table) {
            $table->foreign('sales_id')->references('id')->on('sales')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('uid')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('uid_end')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('storage_assembly', function (Blueprint $table) {
            $table->dropForeign(['sales_id']);
            $table->dropForeign(['uid']);
            $table->dropForeign(['uid_end']);
        });
    }
}
