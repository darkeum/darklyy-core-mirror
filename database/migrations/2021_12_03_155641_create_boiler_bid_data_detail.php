<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateBoilerBidDataDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boiler_bid_data_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('bbd_id')->comment('boiler_bid_type_id');
            $table->integer('cid');
            $table->string('s_sku', 1024)->nullable();
            $table->string('s_name', 1024);
            $table->integer('price_type');
            $table->decimal('s_price')->default(0);
            $table->decimal('s_price_local')->nullable();
            $table->integer('s_unit');
            $table->double('count');
            $table->double('discount')->default(0);
            $table->decimal('discount_sum')->default(0);
            $table->integer('type_nds')->default(3);
            $table->decimal('nds')->default(0);
            $table->decimal('total')->default(0);
            $table->date('date_post')->nullable();
            $table->integer('position')->default(1);
            $table->text('comment')->nullable();
            $table->integer('checkbox')->default(1)->comment('1 - да, 0 - нет');
            
            
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
        Schema::dropIfExists('boiler_bid_data_detail');
    }
}
