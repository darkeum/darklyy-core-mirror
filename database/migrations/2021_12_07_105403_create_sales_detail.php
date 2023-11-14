<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSalesDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('del')->default(0)->comment('Удаление: 0 - есть, 1 - удалено');
            $table->integer('sales_id');
            $table->integer('clone')->nullable();
            $table->integer('cid');
            $table->integer('realization')->nullable();
            $table->integer('supply_detail_id')->nullable()->comment('Связь с supply_detail');
            $table->string('s_sku', 1024)->nullable();
            $table->string('s_name', 1024)->nullable();
            $table->string('s_barcode', 1024)->nullable();
            $table->integer('price_type')->nullable();
            $table->decimal('s_price')->default(0.00);
            $table->decimal('first_price')->nullable()->comment('Сохраненная цена, с первоначальной скидкой boiler');
            $table->text('s_price_all')->nullable();
            $table->decimal('s_price_local')->nullable()->comment('Локальная цена');
            $table->integer('s_unit')->nullable();
            $table->double('count')->default(1);
            $table->double('count_realization')->default(0)->comment('Количество реализованного');
            $table->double('count_assembly')->default(0)->comment('НЕ ИСПОЛЬЗОВАТЬ!!! Количество собранного');
            $table->string('discount', 255)->default(0);
            $table->string('discount_sum', 255)->default(0);
            $table->integer('type_nds')->default(3);
            $table->string('nds', 255)->default(0);
            $table->decimal('total')->default(0.00);
            $table->integer('supply_id')->nullable();
            $table->integer('stock')->nullable();
            $table->float('stock_count')->nullable();
            $table->text('comment')->nullable();
            $table->date('date_post')->nullable();
            $table->integer('local_date_delivery')->nullable();
            $table->integer('position')->nullable();
            $table->integer('status')->default(1);
            $table->text('status_history')->default('[]')->comment('История статусов');
            $table->text('status_log')->default('[]')->comment('JSON. Лог. статусов');
            $table->integer('status_client')->default(1);
            $table->integer('supply')->default(0)->comment('тип поставки: 0 - не указано, 1 - под заказ, 2 - со склада');
            $table->integer('check_update')->nullable()->default(0);

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
        Schema::dropIfExists('sales_detail');
    }
}
