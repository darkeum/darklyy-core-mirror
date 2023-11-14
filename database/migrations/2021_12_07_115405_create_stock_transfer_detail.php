<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateStockTransferDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfer_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('transfer_id')->nullable();
            $table->integer('cid')->nullable();
            $table->integer('sdid')->nullable();
            $table->string('s_name', 256)->nullable();
            $table->string('s_sku', 256)->nullable();
            $table->string('s_unit', 256)->nullable();
            $table->decimal('s_price')->default(0.00);
            $table->double('count')->default(1);
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
        Schema::dropIfExists('stock_transfer_detail');
    }
}
