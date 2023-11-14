<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateCashierShifts extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->integer('uid_start');
            $table->integer('uid_end')->nullable();
            $table->integer('cashiers_id');
            $table->integer('cashbox_id');
            $table->integer('shift_number');
            $table->dateTime('start_shift');
            $table->dateTime('end_shift')->nullable();
            $table->integer('number_checks')->comment('кол-во чеков');
            $table->text('data_start');
            $table->text('data_end');
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
        Schema::dropIfExists('cashier_shifts');
    }
}
