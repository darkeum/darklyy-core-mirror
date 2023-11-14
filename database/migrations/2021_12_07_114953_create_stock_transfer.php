<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateStockTransfer extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfer', function (Blueprint $table) {
            $table->id();
            $table->integer('del')->default(0)->comment('Удаление: 0 - есть, 1 - удалено');
            $table->integer('numb_counter')->nullable();
            $table->string('numb', 255)->nullable();
            $table->string('title', 1024)->nullable()->comment('Название перемещения');
            $table->integer('uid')->nullable();
            $table->integer('uid_gave')->nullable()->comment('Груз отдал');
            $table->integer('uid_accepted')->nullable()->comment('Груз принял');
            $table->integer('object')->nullable();
            $table->integer('org')->nullable()->default(1);
            $table->integer('sales_id')->nullable()->comment('НЕ ИСПОЛЬЗОВАТЬ! Не правильное поле');
            $table->integer('stock_out')->nullable();
            $table->integer('stock_in')->nullable();
            $table->text('comment')->nullable();
            $table->dateTime('date')->nullable();
            $table->dateTime('date_active')->nullable();
            $table->integer('scan')->default(0)->comment('Наличие скана. 0 - нет, 1 - есть');
            $table->integer('has_null')->default(0);
            $table->integer('active')->default(0);
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
        Schema::dropIfExists('stock_transfer');
    }
}
