<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyPtoPrice extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pto_price', function (Blueprint $table) {
            $table->foreign('pto_id')->references('id')->on('pto')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('aid_calc')->references('id')->on('attach')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pto_price', function (Blueprint $table) {
            $table->dropForeign(['pto_id']);
            $table->dropForeign(['aid_calc']);
        });
    }
}
