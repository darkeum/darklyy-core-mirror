<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateOrderPumpStation extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_pump_station', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('order_id')->comment('Идентификатор заявки ');
            $table->integer('r1')->nullable()->comment('Тип системы');
            $table->double('rashod')->nullable()->comment('Расход');
            $table->double('napor')->nullable()->comment('Напор');
            $table->integer('sum')->nullable()->comment('Кол-во насосов');
            $table->integer('sumr')->nullable()->comment('Кол-во резервных');
            $table->integer('tocha')->nullable()->comment('Точка на сколько насосов');
            $table->integer('r2')->nullable()->comment('ПЧ');
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
        Schema::dropIfExists('order_pump_station');
    }
}
