<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateControl extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('control', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->nullable();
            $table->dateTime('date')->useCurrent();
            $table->integer('supply_o')->nullable();
            $table->integer('sales_id')->nullable();
            $table->double('total')->nullable();
            $table->date('plan_pay_date')->nullable();
            $table->tinyInteger('pay')->default(0);
            $table->dateTime('pay_date')->nullable();
            $table->integer('pay_uid')->nullable();
            $table->decimal('pay_total')->nullable();
            $table->integer('oid')->nullable();
            $table->integer('object')->nullable();
            $table->text('comment')->nullable()->comment('Причина отмены оплаты');
            $table->integer('reason')->nullable();
            $table->integer('status')->nullable()->default(1);

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
        Schema::dropIfExists('control');
    }
}
