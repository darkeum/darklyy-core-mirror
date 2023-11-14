<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Golovin Egor
*/

use Boot\Support\Facades\Schema;
use Boot\System\Database\Schema\Blueprint;
use Boot\System\Database\Migrations\Migration;

class KeyControlPaymentTable extends Migration
{
    /**
     * Запуск миграции
     */
    public function up(): void
    {
        Schema::table('control_payment', function (Blueprint $table) {
            $table->foreign('control_id')->references('id')->on('control')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('uid')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Отмена миграции.
     */
    public function down(): void
    {
        Schema::table('control_payment', function (Blueprint $table) {
            $table->dropForeign(['control_id']);
            $table->dropForeign(['uid']);
        });
    }
}
