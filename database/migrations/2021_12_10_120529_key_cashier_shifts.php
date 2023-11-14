<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class KeyCashierShifts extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->foreign('uid_start')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('cashiers_id')->references('id')->on('cashiers')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('cashbox_id')->references('id')->on('cashbox')->onUpdate('restrict')->onDelete('restrict');
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
        Schema::table('cashbox_checks', function (Blueprint $table) {
            $table->dropForeign(['uid_start']);
            $table->dropForeign(['cashiers_id']);
            $table->dropForeign(['cashbox_id']);
            $table->dropForeign(['uid_end']);
        });
    }
}
