<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateStockDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_detail', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 64)->nullable();
            $table->integer('stock')->nullable();
            $table->string('id_1c', 64)->nullable();
            $table->integer('cid')->nullable();
            $table->double('sum')->nullable();
            $table->integer('status')->nullable();

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
        Schema::dropIfExists('stock_detail');
    }
}
