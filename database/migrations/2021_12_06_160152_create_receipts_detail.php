<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateReceiptsDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('rid');
            $table->integer('cid');
            $table->integer('sales_detail_id')->nullable();
            $table->integer('supply_detail_id');
            $table->string('name', 1024)->nullable();
            $table->string('sku', 1024)->nullable();
            $table->string('unit', 1024)->nullable();
            $table->integer('unit_convert')->nullable();
            $table->double('count')->default(0);
            $table->double('count_convert')->nullable();
            $table->decimal('price')->nullable();
            $table->decimal('total')->nullable();
            $table->integer('type_nds')->nullable();
            $table->decimal('nds')->nullable();
            $table->string('comment', 1024)->nullable();
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
        Schema::dropIfExists('receipts_detail');
    }
}
