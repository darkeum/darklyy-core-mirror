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

class CreateControlPaymentTable extends Migration
{
    /**
     * Запуск миграции
     */
    public function up(): void
    {
        Schema::create('control_payment', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('control_id', 20)->comment('Идентификатор контроля');
            $table->date('date')->comment('Дата');
            $table->uid()->comment('Идентификатор пользователя');
        });
    }

    /**
     * Отмена миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_payment');
    }
}
