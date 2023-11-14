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

class CreateControlStorageDetailTable extends Migration
{
    /**
     * Запуск миграции
     */
    public function up(): void
    {
        Schema::create('control_storage_detail', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('control_storage_id', 20)->nullable()->comment('Идентификатор контроля остатков');
            $table->bigInteger('storage_id', 20)->nullable()->comment('Идентификатор пересчета');
            $table->bigInteger('storage_detail_id', 20)->nullable()->comment('Идентификатор позиции пересчета');
            $table->integer('status', 11)->default(1)->comment('Статус');
            $table->timestamps();
        });
    }

    /**
     * Отмена миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_storage_detail');
    }
}
