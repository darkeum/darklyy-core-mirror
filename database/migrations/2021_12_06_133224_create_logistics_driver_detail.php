<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateLogisticsDriverDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logistics_driver_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('driver');
            $table->integer('logistics_detail_id');
            $table->double('count_save');
            $table->double('count');
            $table->integer('status')->default(1);
            $table->dateTime('receipts')->nullable();

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
        Schema::dropIfExists('logistics_driver_detail');
    }
}
