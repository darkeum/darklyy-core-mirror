<?php

use \Boot\Support\Facades\Schema;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\System\Database\Migrations\Migration;

class CreateCorrectDetail extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correct_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('correct_id');
            $table->integer('pid_detail');
            $table->integer('realization_detail_id');
            $table->decimal('s_price')->default(0.00);
            $table->double('count')->default(0);
            $table->integer('s_price_type');
            $table->decimal('discount')->default(0);
            $table->decimal('discount_sum')->default(0.00);
            $table->decimal('nds')->default(0.00);
            $table->integer('nds_type')->default(0.00);
            $table->decimal('total')->default(0.00);

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
        Schema::dropIfExists('correct_detail');
    }
}
