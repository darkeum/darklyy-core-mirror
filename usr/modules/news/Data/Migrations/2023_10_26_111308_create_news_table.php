<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{
    /**
     * Запуск миграции
     */
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->uid();
            $table->string('title', 1024)->comment('Заголовок для новости');
            $table->string('fulltitle', 1024)->comment('Расширенный заголовок для новости');
            $table->text('short')->comment('Короткое описание для новости');
            $table->text('full')->comment('Полный текст новости');

            $table->timestamps();
            //$table->softDeletes();
        });
    }

    /**
     * Отмена миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
}
