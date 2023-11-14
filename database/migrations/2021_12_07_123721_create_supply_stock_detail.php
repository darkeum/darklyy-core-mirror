<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateSupplyStockDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply_stock_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('ssid')->comment('supply stock id');
            $table->integer('cid');
            $table->double('count')->default(0);
            $table->double('count_send')->default(0);
            $table->dateTime('date');
            $table->dateTime('date_edit');
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
        Schema::dropIfExists('supply_stock_detail');
    }
}
