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

class CreateControlDetailTable extends Migration
{
    /**
     * Запуск миграции
     */
    public function up(): void
    {
        Schema::create('control_detail', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('control_id', 11)->nullable()->comment('Идентификатор контроля');
            $table->string('s_name', 1024)->nullable()->comment('Наименование номенклатуры');
            $table->string('s_sku', 1024)->nullable()->comment('Артикул номенклатуры');
            $table->bigInteger('cid', 20)->nullable()->comment('Идентификатор номенклатуры');
            $table->bigInteger('sod_id', 20)->comment('Идентификатор позиции из закупки');
            $table->bigInteger('sdid', 20)->nullable()->comment('Идентификатор позиции из продажи');
            $table->double('count')->comment('Количество');
            $table->double('price_now')->nullable()->comment('Прайс');
            $table->text('comment')->nullable()->comment('Комментарий');
            $table->integer('status', 11)->comment('Статус')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Отмена миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_detail');
    }
}
