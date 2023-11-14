<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateRealizationDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realization_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('rid');
            $table->integer('position')->nullable();
            $table->integer('cid')->nullable();
            $table->integer('sdid')->nullable();
            $table->string('s_name', 1024)->nullable();
            $table->string('s_sku', 256)->nullable();
            $table->decimal('supply_order_price')->nullable()->comment('Сохранение цены закупки');
            $table->decimal('s_price')->nullable();
            $table->integer('s_price_type')->nullable();
            $table->string('s_unit', 11)->nullable();
            $table->double('count')->default(0);
            $table->double('discount')->default(0);
            $table->decimal('discount_sum')->default(0.00);
            $table->decimal('nds')->default(0.00);
            $table->integer('nds_type')->default(3);
            $table->decimal('total')->default(0.00);
            $table->integer('status')->nullable()->default(1);

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
        Schema::dropIfExists('realization_detail');
    }
}
