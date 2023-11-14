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

class KeyControlsStorageDetail extends Migration
{
    /**
     * Запуск миграции
     */
    public function up(): void
    {
        Schema::table('control_storage_detail', function (Blueprint $table) {
            $table->foreign('control_storage')->references('id')->on('control_storage')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('storage_id')->references('id')->on('storage')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('storage_detail_id')->references('id')->on('storage_detail')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Отмена миграции.
     */
    public function down(): void
    {
        Schema::table('control_storage_detail', function (Blueprint $table) {
            $table->dropForeign(['control_storage']);
            $table->dropForeign(['storage_id']);
            $table->dropForeign(['storage_detail_id']);
        });
    }
}
