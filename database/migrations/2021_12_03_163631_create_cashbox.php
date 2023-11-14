<?php

use \Boot\System\Database\Migrations\Migration;
use \Boot\System\Database\Schema\Blueprint;
use \Boot\Support\Facades\Schema;

class CreateCashbox extends Migration
{
    /**
     * Запуск миграции
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashbox', function (Blueprint $table) {
            $table->id();
            $table->string('title', 1024);
            $table->text('address');
            $table->string('coordinates', 1024);
            $table->integer('NumDevice');
            $table->string('KktNumber', 255);
            $table->integer('org');

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
        Schema::dropIfExists('cashbox');
    }
}
