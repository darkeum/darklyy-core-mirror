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

class CreateControlsTable extends Migration
{
    /**
     * Запуск миграции
     */
    public function up(): void
    {
        Schema::create('control', function (Blueprint $table) {
            $table->id();
            $table->uid();
            $table->dateTime('date')->comment('Дата')->useCurrent();
            $table->bigInteger('supply_o', 20)->nullable()->comment('Идентификатор счета поставщика');
            $table->bigInteger('sales_id', 20)->nullable()->comment('Идентификатор продажи');
            $table->double('total')->nullable()->comment('Сумма');
            $table->date('plan_pay_date')->nullable()->comment('Планируемая дата оплаты	');
            $table->tinyInteger('pay', 1)->comment('Статус оплаты')->default(0);
            $table->dateTime('pay_date')->nullable()->comment('Дата оплаты');
            $table->string('pay_num', 1024)->nullable()->comment('Номер платежного поручения');
            $table->uid('pay_uid')->nullable()->comment('Идентификатор оплатившего');
            $table->decimal('pay_total', 10, 2)->nullable()->comment('Сумма оплаты');
            $table->oid();
            $table->bigInteger('object', 20)->nullable()->comment('Идентификатор объекта');
            $table->text('comment')->nullable()->comment('Комментарий');
            $table->integer('reason', 11)->nullable()->comment('Причина отмены оплаты');
            $table->integer('status', 11)->comment('Статус')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Отмена миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('control');
    }
}
