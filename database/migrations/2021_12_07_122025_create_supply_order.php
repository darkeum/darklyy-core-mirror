<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSupplyOrder extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply_order', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('numb_counter');
            $table->string('numb', 255);
            $table->integer('org');
            $table->integer('oid');
            $table->integer('uid');
            $table->integer('object')->nullable();
            $table->integer('sales_id')->nullable()->comment('НЕ ИСПОЛЬЗОВАТЬ! Не правильное поле');
            $table->decimal('total')->nullable()->default(0.00);
            $table->string('bill_code', 255)->nullable();
            $table->date('date_pay')->nullable()->comment('Планируемая дата оплаты');
            $table->integer('delivery')->nullable();
            $table->date('date_bill')->nullable()->comment('Дата счёта');
            $table->date('date_work')->nullable()->comment('Дата размещения');
            $table->date('date_ship')->nullable()->comment('Дата отгрузки');
            $table->date('date_end')->nullable()->comment('Дата поставки');
            $table->date('date_create')->nullable()->useCurrent();
            $table->text('comment')->nullable();
            $table->text('comment_control')->default('[]')->comment('Комментарии контроля');
            $table->decimal('nds')->nullable();
            $table->integer('nds_includes')->default(1);
            $table->integer('control')->nullable()->default(0);
            $table->date('control_send')->nullable();
            $table->integer('pay')->nullable()->default(0);
            $table->integer('arrived')->nullable()->default(0);
            $table->integer('admission')->default(1)->comment('Статус поступления товаров');
            $table->integer('close')->default(0);
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
        Schema::dropIfExists('supply_order');
    }
}
