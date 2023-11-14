<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateStockReservSave extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_reserv_save', function (Blueprint $table) {
            $table->id();
            $table->integer('receipts_detail_id');
            $table->string('module', 1024);
            $table->integer('pid');
            $table->double('count');
            $table->integer('uid');
            $table->integer('stock_id');

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
        Schema::dropIfExists('stock_reserv_save');
    }
}
