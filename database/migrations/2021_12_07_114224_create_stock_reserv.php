<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateStockReserv extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_reserv', function (Blueprint $table) {
            $table->id();
            $table->dateTime('deleted_at')->nullable()->comment('Маркер удаления');
            $table->integer('cid');
            $table->integer('stock_id');
            $table->integer('uid');
            $table->string('module', 255);
            $table->integer('pid');
            $table->double('count');
            $table->dateTime('date')->useCurrent();
            $table->integer('status')->comment('0 - удален, 1 - есть, 2 - будущий резерв');

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
        Schema::dropIfExists('stock_reserv');
    }
}
