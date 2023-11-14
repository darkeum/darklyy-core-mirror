<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyStorageAssemblyDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_assembly_detail', function (Blueprint $table) {
            $table->foreign('said')->references('id')->on('storage_assembly')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('sdid')->references('id')->on('sales_detail')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('storage_assembly_detail', function (Blueprint $table) {
            $table->dropForeign(['said']);
            $table->dropForeign(['sdid']);
        });
    }
}
