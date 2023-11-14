<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateCashboxChecks extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashbox_checks', function (Blueprint $table) {
            $table->id();
            $table->integer('uid');
            $table->integer('realization_id');
            $table->integer('cashier_shifts_id');
            $table->text('data');
            $table->integer('TypeCheck');

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
        Schema::dropIfExists('cashbox_checks');
    }
}
