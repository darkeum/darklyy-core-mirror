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

class KeyControlsTable extends Migration
{
    /**
     * Запуск миграции
     */
    public function up(): void
    {
        Schema::table('control', function (Blueprint $table) {
            $table->foreign('uid')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('oid')->references('id')->on('organizations')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('object')->references('id')->on('object')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('sales_id')->references('id')->on('sales')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('supply_o')->references('id')->on('supply_order')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('pay_uid')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Отмена миграции.
     */
    public function down(): void
    {
        Schema::table('control', function (Blueprint $table) {
            $table->dropForeign(['uid']);
            $table->dropForeign(['oid']);
            $table->dropForeign(['object']);
            $table->dropForeign(['sales_id']);
            $table->dropForeign(['supply_o']);
            $table->dropForeign(['pay_uid']);
        });
    }
}
