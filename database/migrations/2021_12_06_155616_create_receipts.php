<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateReceipts extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('numb_counter');
            $table->string('numb', 255)->nullable();
            $table->integer('sales_id')->nullable()->comment('ИД Продажи');
            $table->integer('supply_id')->comment('ИД Счета');
            $table->integer('org')->default(1)->comment('Организация');
            $table->integer('object')->nullable()->comment('Объект');
            $table->integer('stock_id')->default(1)->comment('ИД Склада');
            $table->integer('uid')->comment('Идентификатор ответственного');
            $table->integer('oid')->comment('ИД Поставщика');
            $table->string('oid_name', 1024)->comment('Поставщик');
            $table->string('inn', 255)->comment('ИНН поставщика');
            $table->dateTime('date')->comment('Дата создания');
            $table->date('date_receipts')->nullable()->comment('Дата поступления');
            $table->string('doc_number', 255)->nullable()->comment('Номер входящего документа');
            $table->integer('type_doc')->nullable()->default(0)->comment('тип документа(1 - УПД; 2 - счет фактура/товарная накладная))');
            $table->string('waybill_number', 255)->nullable()->comment('номер товарной накладной');
            $table->string('total', 255)->nullable()->comment('Сумма');
            $table->decimal('nds')->nullable()->comment('НДС');
            $table->tinyInteger('nds_includes')->nullable()->default(1)->comment('НДС В том числе');
            $table->integer('syncRegister')->default(1);
            $table->integer('active')->default(0)->comment('Статус проведения');
            $table->integer('status')->default(1)->comment('Статус');

            $table->timestamps();
        });
    }

    /**
     * Откат миграции
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receipts');
    }
}
