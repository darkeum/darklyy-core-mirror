<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateStockTransaction extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transaction', function (Blueprint $table) {
            $table->id();
            $table->integer('cid')->nullable()->comment('Идентификатор товара');
            $table->integer('stock_id')->nullable()->comment('Идентификатор склада');
            $table->integer('uid')->nullable()->comment('Отвественный');
            $table->decimal('initial_value')->comment('Начальное значение');
            $table->string('sign', 24)->nullable()->comment('Операция');
            $table->double('count')->comment('Количество');
            $table->double('sum')->nullable()->comment('Итоговое значение');
            $table->dateTime('date')->nullable()->comment('Дата');
            $table->string('module', 255)->nullable()->comment('Родительский модуль');
            $table->integer('pid')->nullable()->comment('Идетификатор родительской записи');
            $table->text('comment')->nullable()->comment('Комментарий');
            $table->integer('status')->nullable()->comment('Статус');

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
        Schema::dropIfExists('stock_transaction');
    }
}
