<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateShipment extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment', function (Blueprint $table) {
            $table->id();
            $table->integer('cid');
            $table->string('module', 255);
            $table->integer('pid');
            $table->integer('uid');
            $table->integer('stock_id');
            $table->double('initial_value')->comment('Начальное значение');
            $table->double('count')->comment('Количество');
            $table->double('sum')->comment('Итоговое значение');
            $table->dateTime('date');
            $table->text('comment')->nullable();

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
        Schema::dropIfExists('shipment');
    }
}
