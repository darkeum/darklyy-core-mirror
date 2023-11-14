<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSupplyOrderDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply_order_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('supply_o');
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('cid');
            $table->integer('sale_detail_id')->nullable();
            $table->integer('supply_d')->nullable();
            $table->integer('supply_s')->nullable()->comment('supply_stock_detail');
            $table->string('s_sku', 1024)->nullable();
            $table->string('s_name', 1024)->nullable();
            $table->double('count')->default(1);
            $table->double('count_receipts')->default(0)->comment('НЕ ИСПОЛЬЗОВАТЬ!!! количество поступления (receipts)');
            $table->text('comment')->nullable();
            $table->decimal('price_now')->nullable()->default(0.00);
            $table->decimal('total')->nullable()->default(0.00);
            $table->integer('type_nds')->default(3);
            $table->decimal('nds')->default(0.00);
            $table->integer('unit')->nullable();
            $table->date('date_ship')->nullable();
            $table->date('date_end')->nullable();
            $table->integer('status')->default(1);

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
        Schema::dropIfExists('supply_order_detail');
    }
}
