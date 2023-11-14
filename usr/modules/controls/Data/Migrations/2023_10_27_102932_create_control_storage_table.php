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

class CreateControlStorageTable extends Migration
{
    /**
     * Запуск миграции
     */
    public function up(): void
    {
        Schema::create('control_storage', function (Blueprint $table) {
            $table->id();
            $table->string('title', 1024)->nullable()->comment('Заголовок');
            $table->dateTime('date')->useCurrent()->comment('Дата');
            $table->integer('status', 11)->default(1)->comment('Статус');
            $table->timestamps();
        });
    }

    /**
     * Отмена миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_storage');
    }
}
