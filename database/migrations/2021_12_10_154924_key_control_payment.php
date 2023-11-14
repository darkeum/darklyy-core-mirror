<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyControlPayment extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('control_payment', function (Blueprint $table) {
            $table->foreign('control_id')->references('id')->on('control')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::table('control_detail', function (Blueprint $table) {
            $table->dropForeign(['control_id']);
            $table->dropForeign(['uid']);
        });
    }
}
