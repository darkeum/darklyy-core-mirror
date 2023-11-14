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

class KeyControlDetailTable extends Migration
{
    /**
     * Запуск миграции
     */
    public function up(): void
    {
        Schema::table('control_detail', function (Blueprint $table) {
            $table->foreign('control_id')->references('id')->on('control')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('sdid')->references('id')->on('sales_detail')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('sod_id')->references('id')->on('supply_order_detail')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('cid')->references('id')->on('catalog')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Отмена миграции.
     */
    public function down(): void
    {
        Schema::table('control_detail', function (Blueprint $table) {
            $table->dropForeign(['control_id']);
            $table->dropForeign(['sdid']);
            $table->dropForeign(['sod_id']);
            $table->dropForeign(['cid']);
        });
    }
}
